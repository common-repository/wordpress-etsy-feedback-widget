<?php

$cache_path = ABSPATH . 'wp-content/';

// get options
$options = get_option('wp_etsy_feedback');
$id = $options['etsy_id'];
$etsy_count = $options['etsy_count'];



// check whether we know the users id
if ($id == '') {
    echo ("<b>Set your id!</b><br/>");
} else {
// decide whether to use the cached feedback
    $age = time() - $options['last_update_time'];
    $max_age = 10; //10 seconds
    if ($age > $max_age) {
        $options['last_update_time'] = time();


        // get the feedback
        $url = "http://openapi.etsy.com/v2/public/users/$id/feedback/as-subject?limit=50&api_key=f56k3tgsg9fuy2bn9ej83nkx";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (intval($status) != 200)
            echo("Error: $response_body");
        $response = json_decode($response_body);

        // asseble feedback html
        $feedback_html = "<a href=\"http://www.etsy.com/people/$id/feedback\">" .  $response->count . ' feedback</a><br/>';
        $feedback_html .= "showing $etsy_count most recent<br/>";
        $added = 0;
        foreach ($response->results as $result) {
            if ($result->message != '' && $result->target_user_id == $result->seller_user_id && $added < $etsy_count) {
                $feedback_html .= ' <a href="http://www.etsy.com/transaction/' . $result->transaction_id . '">"' . $result->message . '"</a><br/><br/>';
                $added++;
            }
        }

        // store it as an option
        $options['cached_text'] = $feedback_html;
        update_option('wp_etsy_feedback', $options);
    }

    // echo the contents of the cache file
    echo($options['cached_text']);
}
?>
