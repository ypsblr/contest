<?php
$message  = "<html><body>";
   
$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
   
$message .= "<tr><td>";
   
$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
    
$message .= "<thead>
				<tr height='60'>
					<th colspan='4' style='background-color:#f5f5f5; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:26px;' >
						Karnataka Tourism Monthly Photo Contest
					</th>
				</tr>
             </thead>";
    
$message .= "<tbody>
				<tr>
					<td colspan='4' style='padding:15px;'>
						<p style='font-size:19px;'>There is a query from " . $full_name . ",</p>
						<hr />
						<p style='font-size:12px;'>Contact Query ...</p>
						<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'> <b>My Query is :</b>  " . $query . ".</p>
					</td>
				</tr>
      
				<tr height='40'>
					<td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px; '>
						<label>Email ID: </label>" . $emailid . "
					</td>
       
					<td style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:17px; '>
						<label>Phone: </label>".$phone."
					</td>

					</tr>
			</tbody>";
    
$message .= "</table>";
   
$message .= "</td></tr>";
$message .= "</table>";
$message .= "Please respond at the earliest !<br><br>";
$message .= "- YPS Contest Admin<br>";
$message .= "</body></html>";
?>