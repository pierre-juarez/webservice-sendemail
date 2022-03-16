<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$ruta = $_SERVER['DOCUMENT_ROOT'].'/webservice-matricula_upg_fii/vendor/phpmailer/src/PHPMailer.php';

include($ruta);
include('config/config.php');

 
    // Ruta: http://localhost/webservice-matricula_upg_fii/index.php

    //Definimos con header el tipo del documento (JSON)
    header("Content-Type:application/json");
    
    /** Validamos que solo acepte método POST */    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(!empty($_POST['correo']) && !empty($_POST['asunto']) && !empty($_POST['bodyMail']) )
        {
            $correo = $_POST['correo'];
            $asunto = $_POST['asunto'];
            $body = $_POST['bodyMail'];
                              
            /** Enviamos el correo electrónico, pasándole los parámetros necesarios */
            enviarCorreo($correo, $asunto, $body);
           
        }
        else
        {
            //entregamos respuesta 
            respuesta_entregada(200, "No se pudo completar tu solicitud", null);
        }

    }
    //validamos varaiables vacias
 
    //funcion que crea la respuesta, con estado, mensaje de estados y datos
    function respuesta_entregada($estado, $mensaje_estado, $datos){
        //cabecera respuesta
        header("HTTP/1.1 $estado $mensaje_estado");
        
        //rellenamos array con estado, mensaje y datos
        $respuesta['estado'] = $estado;
        $respuesta['mensaje_estado'] = $mensaje_estado;
        $respuesta['status'] = $datos;
  
        //codificamos el json
        $respuesta_json = json_encode($respuesta);
 
        //pintamos el contenido del json
        echo $respuesta_json;
    }

  
    function enviarCorreo($correo, $asunto, $body){
                
        $mail = new PHPMailer();
        $mail->IsSMTP();
            
        $mail->Host = HOST_MAIL;
        $mail->Port = PUERTO_MAIL;
        $mail->From = REMITENTE_MAIL;
        $mail->SMTPAuth = AUTH_MAIL;
        $mail->SMTPSecure = SEGURIDAD_MAIL;                
        
        $mail->Username = CORREO_SISTEMA;
        $mail->Password = PASSWORD_CORREO;	
        
        $mail->setFrom(CORREO_SISTEMA, NOMBRE_SISTEMA);
        $mail->AddAddress($correo);//Agregar destinatario
        $mail->Subject = $asunto;

        $body = $body;

        $mail->AltBody = ".";
        $mail->MsgHTML($body);
        $mail->IsHTML(true);
        $mail->CharSet = CHARSET_MAIL;// Activa la condificacción utf-8        
        // $mail->SMTPDebug  = 3;         

        if ($mail->Send()) {
            //Correo enviado                        
            respuesta_entregada(200, "Correo enviado correctamente", true);

        }else{
            //Error al enviar
            respuesta_entregada(500, "Error".$mail->ErrorInfo, $correo);        
        }
    }

?>