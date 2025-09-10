<?php
$mail_msg=" ";
$cont=0;							  
foreach ($_POST['img_title'] as $key=>$value ) {
$cont+=1;
$type_title = mysqli_real_escape_string($DBCON, $value);
$mail_msg.="<tr height='40'>
       <td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px;'>
       <label>Email ID: </label>".$type."
       </td>
       
       <td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px;'>
       <label>Phone: </label>".$type_title ."
       </td>
       </tr>";
}
						  
$message  = "<html><body>";
   
$message .= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
   
$message .= "<tr><td>";
   
$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:590px; background-color:#fff; font-family:arial,sans-serif;'>";
    
$message .= "<thead>
  <tr height='60'>
  <th colspan='4' style='background-color:#f5f5f5; border-bottom:solid 1px #bdbdbd; font-family:arial,sans-serif; color:#333; font-size:20px;' >34th YPS All India Digital Salon 2016</th>
  </tr>
             </thead>";
  
$message .= "<tbody>
       <tr>
       <td colspan='4' style='padding:5px;'>
       <p style='font-size:17px;'>Hi sir I am ' ".$sender.", & Reg.ID:- YPS00".$uid."</p>
       <hr />
       <p style='font-size:16px;'> I have uploaded ".$cont." images ,</p>
       </td>
       </tr>

	  <tr height='40'>
       <td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px;'>
       <b>Catagory </b>
       </td>
       
       <td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px;'>
       <b>Image Title: </b>
       </td>
       </tr>".$mail_msg."
        </tbody>";
    
$message .= "</table>";
   
$message .= "</td></tr>";
$message .= "</table>";
   
$message .= "</body></html>";

?>