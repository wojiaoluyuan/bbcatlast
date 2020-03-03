<?php
/**
 * Copyright (c) 2019-2025, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since 2.5.0
 * @package BBcat-K
 * @author 洛茛艺术影视在线
 * @date 2019-04-03 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

/**
 * Class AsyncEmail
 */
final class AsyncEmail extends WPAsyncTask {
    protected $action = 'send_mail';

    protected $argument_count = 5;

    /**
     * Prepare data for the asynchronous request
     *
     * @throws Exception If for any reason the request should not happen
     *
     * @param array $data An array of data sent to the hook
     *
     * @return array
     */
    protected function prepare_data( $data ) {
        // $from, $to, $title = '', $args = array(), $template = 'comment'
        return array(
            'from' => $data[0],
            'to' => $data[1],
            'title' => $data[2],
            'args' => $data[3],
            'template' => $data[4]
        );
    }

    /**
     * Run the async task action
     */
    protected function run_action() {
        //$data = $this->_body_data;
        $args = json_decode(base64_decode($_POST['args']));
        $args = $args ? (array)$args : $_POST['args'];
        $data = array(
            'from' => $_POST['from'],
            'to' => $_POST['to'],
            'title' => $_POST['title'],
            'args' => $args,
            'template' => $_POST['template']
        );
        $action = $_POST['action'];
        do_action( $action, $data['from'], $data['to'], $data['title'], $data['args'], $data['template'] );
        // 也可以直接tt_mail($data['from'], $data['to'], $data['title'], $data['args'], $data['template']), 则不需要在tt_mail下写add_action('tt_async_send_mail', xx);
    }
}