<?php 
function dd($post_objects){
    echo '<pre>';
    print_r( $post_objects  );
    echo '</pre>';
    die;
}

if ($_SERVER['HTTP_HOST']=='wordpress.dev') {
  add_action( 'phpmailer_init', 'mailer_config', 10, 1);
  function mailer_config(PHPMailer $mailer){
    $mailer->IsSMTP();
    // $mailer->Host = "smtp.wlink.com.np"; // your SMTP server
    // $mailer->Port = 25;
    $mailer->Host = "localhost"; // your SMTP server
    $mailer->Port = 1025;
    $mailer->SMTPDebug = 2; // write 0 if you don't want to see client/server communication in page
    $mailer->CharSet  = "utf-8";
  }

  add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
  function log_mailer_errors($wp_error){
    $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
    $fp = fopen($fn, 'a');
    fputs($fp, "Mailer Error: " . $mailer->ErrorInfo ."\n");
    fclose($fp);
  }      
}
