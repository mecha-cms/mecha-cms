<?php


/**
 * Create New Comment
 * ------------------
 */

if( ! function_exists('do_comment_construct')) {
    function do_comment_construct() {
        $config = Config::get();
        $speak = Config::speak();
        if($config->page_type === 'article') {
            $comment_id = 'comment-%d'; // Your comment ID
            $comment_form_id = 'comment-form'; // Your comment form ID
            $article = isset($config->article->path) ? $config->article : false;
            $G = array('data' => array(
                'article' => Mecha::A($article),
                'comment_id' => $comment_id,
                'comment_form_id' => $comment_form_id
            ));
            if($article !== false && $request = Request::post()) {
                if($task = File::exist(SHIELD . DS . $config->shield . DS . 'workers' . DS . 'task.comment.php')) {
                    require $task; // Custom comment constructor
                } else {
                    // Check token
                    Guardian::checkToken($request['token'], $article->url . '#' . $comment_form_id);
                    $extension = $config->comments->moderation && ! Guardian::happy() ? '.hold' : '.txt';
                    // Check name
                    if(trim($request['name']) === "") {
                        Notify::error(Config::speak('notify_error_empty_field', $speak->name));
                    }
                    // Check email
                    if(trim($request['email']) !== "") {
                        if( ! Guardian::check($request['email'], '->email')) {
                            Notify::error($speak->notify_invalid_email);
                        } else {
                            // Disallow passenger(s) from entering your email address in the comment email field
                            if( ! Guardian::happy() && $request['email'] === $config->author->email) {
                                Notify::warning(Config::speak('notify_warning_forbidden_input', array('<em>' . $request['email'] . '</em>', strtolower($speak->email))));
                            }
                        }
                    } else {
                        Notify::error(Config::speak('notify_error_empty_field', $speak->email));
                    }
                    // Check URL
                    if(trim($request['url']) !== "" && ! Guardian::check($request['url'], '->url')) {
                        Notify::error($speak->notify_invalid_url);
                    }
                    // Check message
                    if(trim($request['message']) === "") {
                        Notify::error(Config::speak('notify_error_empty_field', $speak->message));
                    }
                    // Check challenge
                    if( ! Guardian::checkMath($request['math'])) {
                        Notify::error($speak->notify_invalid_math_answer);
                    }
                    // Check name length
                    if(Guardian::check($request['name'], '->too_long', 100)) {
                        Notify::error(Config::speak('notify_error_too_long', $speak->name));
                    }
                    // Check email length
                    if(Guardian::check($request['email'], '->too_long', 100)) {
                        Notify::error(Config::speak('notify_error_too_long', $speak->email));
                    }
                    // Check URL length
                    if(Guardian::check($request['url'], '->too_long', 100)) {
                        Notify::error(Config::speak('notify_error_too_long', $speak->url));
                    }
                    // Check message length
                    if(Guardian::check($request['message'], '->too_long', 1700)) {
                        Notify::error(Config::speak('notify_error_too_long', $speak->message));
                    }
                    // Check for spam keyword(s) in comment
                    $fucking_words = explode(',', $config->keywords_spam);
                    foreach($fucking_words as $spam) {
                        if($fuck = trim($spam)) {
                            if(
                                $request['email'] === $fuck || // Block by email address
                                strpos(strtolower($request['message']), strtolower($fuck)) !== false // Block by message word(s)
                            ) {
                                Notify::warning($speak->notify_warning_intruder_detected . ' <strong class="text-error pull-right">' . $fuck . '</strong>');
                                break;
                            }
                        }
                    }
                    if( ! Notify::errors()) {
                        $post = Date::slug($article->time);
                        $id = (int) time();
                        $parent = Request::post('parent');
                        $P = array('data' => $request);
                        $P['data']['id'] = $id;
                        $name = strip_tags($request['name']);
                        $email = Text::parse($request['email'], '->broken_entity');
                        $url = isset($request['url']) && trim($request['url']) !== "" ? $request['url'] : false;
                        $parser = strip_tags(Request::post('content_type', $config->html_parser));
                        $message = $request['message'];
                        $field = Request::post('fields', array());
                        include File::D(__DIR__, 2) . DS . 'task.fields.php';
                        $message = strip_tags($message, '<br><img>' . ($parser === 'HTML' || $parser === false ? '<a><abbr><b><blockquote><code><del><dfn><em><i><ins><li><ol><p><pre><span><strong><sub><sup><time><u><ul><var>' : ""));
                        // Temporarily disallow image(s) in comment to prevent XSS
                        $message = preg_replace('#(\!\[.*?\]\(.*?\))#','`$1`', $message);
                        $message = preg_replace('#<img(\s[^<>]*?)>#i', '&lt;img$1&gt;', $message);
                        // Disallow `{{php}}` shortcode in comment to prevent PHP script injection
                        $message = str_replace(array('{{php}}', '{{/php}}'), "", $message);
                        Page::header(array(
                            'Name' => $name,
                            'Email' => $email,
                            'URL' => $url,
                            'Status' => Guardian::happy() ? 1 : 2,
                            'Content Type' => $parser,
                            'Fields' => ! empty($field) ? Text::parse($field, '->encoded_json') : false
                        ))->content($message)->saveTo(COMMENT . DS . $post . '_' . Date::slug($id) . '_' . ($parent ? Date::slug($parent) : '0000-00-00-00-00-00') . $extension);
                        Notify::success(Config::speak('notify_success_submitted', $speak->comment));
                        if($extension === '.hold') {
                            Notify::info($speak->notify_info_comment_moderation);
                        }
                        Weapon::fire(array('on_comment_update', 'on_comment_construct'), array($G, $P));
                        Guardian::kick($config->url_current . ( ! Guardian::happy() && $config->comments->moderation ? '#' . $comment_form_id : '#' . sprintf($comment_id, Date::format($id, 'U'))));
                    } else {
                        Guardian::kick($config->url_current . '#' . $comment_form_id);
                    }
                }
            }
        }
    }
}

Weapon::add('shield_before', 'do_comment_construct');