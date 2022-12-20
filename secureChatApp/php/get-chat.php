<?php 
    session_start();
    include_once "../header.php";
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);

        

        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){

                
                if($row['outgoing_msg_id'] === $outgoing_id){

                    // Chat decryption
        
                    $encrypted_message = $row['msg'] ;
                    $method = "AES-128-CTR";
                    $iv_length = openssl_cipher_iv_length($method);
                    $iv = '6204610323130699';
                    $secret_key = "SecureChat";
                    $decrypted_message = openssl_decrypt($encrypted_message, $method, $secret_key, 0, $iv);

                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'.$decrypted_message.'</p>
                                </div>
                                </div>';
                }else{

                    $encrypted_message = $row['msg'] ;
                    $method = "AES-128-CTR";
                    $iv_length = openssl_cipher_iv_length($method);
                    $iv = '6204610323130699';
                    $secret_key = "SecureChat";
                    $decrypted_message = openssl_decrypt($encrypted_message, $method, $secret_key, 0, $iv);

                    $output .= '<div class="chat incoming">
                                <div class="details">
                                    <p>'.$decrypted_message.'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Start a new conversation!</div>';
        }
        echo $output;
    }else{
        header("location:index.php");
    }

?>
