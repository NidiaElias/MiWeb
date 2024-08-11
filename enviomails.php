<!-- ESTE ES EL ARCHIVO QUE RECIBE EL CONTENIDO Y ENVÍA LOS MAILS -->
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

<?php

$servidor = "";
$usuario_mail = "";
$password_mail = "";

require("class.phpmailer.php");
require("class.smtp.php");
//si no encuentro datos de acceso externo lo lleno a mano
//completar con datos reales o no funcionará
if (!@include_once("datos_de_acceso.php")){
  $servidor = 'aaa.com';
  $usuario_mail = 'clase@a.com.ar';
  $password_mail = '1234';
}



$mantenimiento = false;
$ipcliente = $_SERVER['REMOTE_ADDR'];
$iprequerida = "179.51.239.42";

// echo $mantenimiento;
// echo $ipcliente;

if($mantenimiento && $ipcliente != $iprequerida){
  echo "We are doing maintenance. We'll be back in a few minutes";
}
else
{
  $resultado = true;
  // --- Levanto variables de POST para enviar el mail
  
  $nombre = $_POST['nombre'];
  $email = $_POST['mail'];
  $telefono = $_POST['telefono'];
  $mensaje = $_POST['mensaje'];
  // Fin de lectura de variables de POST
  $uuid = uniqid();


  // --- Mail que se envía a la cuenta de la página (lo que recibe Nidia)

  $to['email'] = "nidia.c.elias@gmail.com"; //mail en el que recibo las consultas
  // La cuenta que va acá arriba, puede ser una cuenta externa, ej: gmail de Nidia     
  $to['name'] = "Nidia Catalina Elías";   
  $subject = "Consulta via web"; //Asunto del mensaje
  $str = $mensaje; //Cuerpo del mail
  $mail = new PHPMailer;
  $mail->IsSMTP();                                     
  $mail->SMTPAuth = true;
  $mail->Host = $servidor; //Siempre igual, es la web
  $mail->Port = 465;
  $mail->Username = $usuario_mail; //Cuenta real creada en nuestra web
  // ^^ La cuenta que va acá arriba SÍ O SÍ es una cuenta real que envía mails desde nuestra web.
  $mail->Password = $password_mail; //Contraseña real de la cuenta de mail en nuestra web
  $mail->SMTPSecure = 'ssl';
  $mail->From = $usuario_mail; // Cuenta que envía desde el servidor. Igual a $mail->Username
  $mail->FromName = "Consulta de $nombre"; // Nombre de quién envía, se llena a mano a gusto
  $mail->AddReplyTo($email, $nombre);  //A quién responder el mail. Igual a $mail->Username
  $mail->AddAddress($to['email'],$to['name']);
  $mail->Priority = 1;
  $mail->AddCustomHeader("X-MSMail-Priority: High");
  $mail->WordWrap = 50;    
  $mail->IsHTML(true);  
  $mail->Subject = $subject;
  $mail->Body    = $str;
  if(!$mail->Send()) {
    $err = 'Message could not be sent.';
    $err .= 'Mailer Error: ' . $mail->ErrorInfo; 
    $resultado = false;                       
  }

  $mail->ClearAddresses();

  //Mail de confirmación que se envía al usuario
  $subject = "Tu consulta de clases"; //Asunto
  $str = "<i>Tu consulta fue enviada, te responderemos a la brevedad.<br> ";
  $str .= "Número de consulta: $uuid </i>";
  // ^^ Todo lo anterior es el cuerpo del mail.
  $mail = new PHPMailer;
  $mail->IsSMTP();                                     
  $mail->SMTPAuth = true;
  $mail->Host = $servidor;
  $mail->Port = 465;
  $mail->Username = $usuario_mail; //Cuenta real que envía el mail desde nuestra web
  $mail->Password = $password_mail; //Contraseña
  $mail->SMTPSecure = 'ssl';
  $mail->From = 'no-reply@msishop.com.ar'; // no-reply quiere decir que el cliente no puede contestar el mail
  $mail->FromName = "Clases de matemática";
  $mail->AddReplyTo('no-reply@msishop.com.ar', 'Clases de matemática'); //Igual a $mail->From 
  $mail->AddAddress($email, $nombre);
  $mail->Priority = 1;
  $mail->AddCustomHeader("X-MSMail-Priority: High");
  $mail->WordWrap = 50;    
  $mail->IsHTML(true);  
  $mail->Subject = $subject;
  $mail->Body    = $str;
  if(!$mail->Send()) {
    $err = 'Message could not be sent.';
    $err .= 'Mailer Error: ' . $mail->ErrorInfo;  
    $resultado = false;                      
  }

  if($resultado)
  {
    // Mensaje de mail enviado correctamente que se muestra en la web.
    echo  "<i> Su solicitud fue enviada correctamente </i>";
  }
  else{
    // Error que muestra el navegador si el mail salió mal.
    echo "<i> Algo falló, inténtelo de nuevo más tarde</i>";
  }

  echo "<script>
  //Adónde me lleva la página después de enviar el mail (correctamente o no)
  setTimeout(\"location.href = 'https://www.msishop.com.ar/nidia';\",2500);
  </script>
  ";
}
?>