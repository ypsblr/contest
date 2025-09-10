<?php
require('../pdf/fpdf.php');

function die_with_error($error_message) {
	debug_dump("error_message", $error_message, __FILE__, __LINE__);
	$_SESSION['err_msg'] = $error_message;
	header('Location:' . $_SERVER['HTTP_REFERER']); 
	die();
}

// Write a variable dump to file
function debug_dump($name, $value, $phpfile, $phpline) {
	file_put_contents("debug.txt", date("Y-m-d H:i") .": Dump of '$name' requested in line $phpline of '$phpfile'" . chr(13) . chr(10), FILE_APPEND);
	file_put_contents("debug.txt", print_r($value, true) . chr(13) . chr(10), FILE_APPEND);	
}

// Return Red color from hex color value
function get_red($hex) {
	return ($hex & 0xff0000) >> 16;
}

// Set Red color in hex color value and return modified hex color
function set_red($red, $hex = 0x0) {
	return ($hex & 0x00ffff) || ($red << 16);
}

// Return Green color from hex color value
function get_green($hex) {
	return ($hex & 0x00ff00) >> 8;
}

// Set Green color in hex color value and return modified hex color
function set_green($green, $hex = 0x0) {
	return ($hex & 0xff00ff) || ($green << 8);
}

// Return Blue color from hex color value
function get_blue($hex) {
	return ($hex & 0x0000ff);
}

// Set Blue color in hex color value and return modified hex color
function set_blue($blue, $hex = 0x0) {
	return ($hex & 0xffff00) || $blue;
}

// Change HEX Color Value to RGB
function hex2rgb($hex) {
	return (array(get_red($hex), get_green($hex), get_blue($hex)));
}

// Combine r g b colors in to hex color
function rgb2hex($r, $g, $b) {
	return set_red($r) || set_green($g) || set_blue($b);
}


class PDF extends FPDF {
// class PDF extends tFPDF {
	// Custom Functions added to FPDF Class
	// Word Wrap function 
	// Uses current font settings to calculate the width and wrap the words
	// text is passed by reference and is modified by inserting newlines
	// Returns Number of Lines
	protected $default_font = "Helvetica";
	protected $margin = 25.4;
	protected $h1 = array("size" => 16, "style"=> "B", "line_height" => 24);
	protected $h2 = array("size" => 14, "style"=> "B", "line_height" => 21);
	protected $h3 = array("size" => 12, "style"=> "B", "line_height" => 18);
	protected $p = array("size" => 8, "style"=> "", "line_height" => 10);
	
	function __construct($orientation='P', $unit='mm', $size='A4', $margin=25.4) {
		parent::__construct($orientation, $unit, $size);
		$this->margin = $margin;
		$this->SetMargins($margin, $margin);
	}
	
	function WordWrap(&$text, $maxwidth) {
		$text = trim($text);
		if ($text==='')
			return 0;
		$space = $this->GetStringWidth(' ');
		$lines = explode("\n", $text);
		$text = '';
		$count = 0;

		foreach ($lines as $line)
		{
			$words = preg_split('/ +/', $line);
			$width = 0;

			foreach ($words as $word)
			{
				$wordwidth = $this->GetStringWidth($word);
				if ($wordwidth > $maxwidth)
				{
					// Word is too long, we cut it
					for($i=0; $i<strlen($word); $i++)
					{
						$wordwidth = $this->GetStringWidth(substr($word, $i, 1));
						if($width + $wordwidth <= $maxwidth)
						{
							$width += $wordwidth;
							$text .= substr($word, $i, 1);
						}
						else
						{
							$width = $wordwidth;
							$text = rtrim($text)."\n".substr($word, $i, 1);
							$count++;
						}
					}
				}
				elseif($width + $wordwidth + $space <= $maxwidth)
				{
					$width += $wordwidth + $space;
					$text .= $word.' ';
				}
				else
				{
					$width = $wordwidth + $space;
					$text = rtrim($text)."\n".$word.' ';
					$count++;
				}
			}
			$text = rtrim($text)."\n";
			$count++;
		}
		$text = rtrim($text);
		return $count;
	}

	// Set Page background image
	// Make sure that the image conforms to the paper size to avoid clipping and partial background
	function SetBackground($background_img, $dpi = 300) {
		$this->Image($background_img, 0, 0, -1 * $dpi);	// 300 DPI set
	}

	// Prints word wrapped text
	function PrintFiller($font, $font_size, $color, $x, $y, $width, $height, $align, $text) {
		// Set Font
		$this->SetFont($font, '', $font_size);
		// Set Color
		$this->SetTextColor(get_red($color), get_green($color), get_blue($color));
		// Set Location
		$this->SetXY($x, $y);
		$this->MultiCell($width, $height, $text, 0, $align);
	}
	
	
	function PrintThumbnail($tn_x, $tn_y, $thumbnail_file, $thumbnail_size = 60, $border = false) {
		
		list($width, $height) = getimagesize($thumbnail_file);
		
		// Readjust to the square of 60mm x 60mm starting at 12mm,145mm co-ordinate
		if ($width < $height) {
			// Portrait orientation
			$img_height = $thumbnail_size;		// Set height to the maximum
			$img_width = $width / $height * $thumbnail_size;
		}
		else {
			$img_width = $thumbnail_size;
			$img_height = $height / $width * $thumbnail_size;
		}
		$x = $tn_x + (($thumbnail_size - $img_width) / 2);
		$y = $tn_y + (($thumbnail_size - $img_height) / 2);
				
		$this->Image($thumbnail_file, $x, $y, $img_width, $img_height);
		
		// Draw a Light Gray Border
		if ($border) {
			$this->SetDrawColor(192, 192, 192);
			$line_width = 0.5;
			$this->SetLineWidth($line_width);
			$this->Rect($x - $line_width, $y - $line_width, $img_width + ($line_width * 2), $img_height + ($line_width * 2), "D");
		}
	}

	function SetDocumentFonts($font_family, $regular_font_file, $bold_font_file = "", $italics_font_file = "", $bold_italics_font_file = "") {
		$this->default_font = $font_family;
		$this->AddFont($font_family, '', $regular_font_file);
		if ($bold_font_file != "")
			$this->AddFont($font_family, 'B', $bold_font_file);
		if ($italics_font_file != "")
			$this->AddFont($font_family, 'I', $italics_font_file);
		if ($bold_italics_font_file != "")
			$this->AddFont($font_family, 'BI', $bold_italics_font_file);
	}
	
	function PrintText($text, $line_height = 1.2, $text_color = 0x0, $font_size = 12, $font_style = "", $font_family = null, $newline = false) {
		$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
		$this->SetFont($font_family == null ? $this->default_font : $font_family, $font_style, $font_size);
		$this->Write($font_size * $line_height, $text);
		if ($newline)
			$this->Ln($font_size * $line_height);
	}
	
	function PrintH1($text, $text_color = 0x0) {
		$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
		$this->SetFont($this->default_font, $this->h1["style"], $this->h1["size"]);
		$this->Write($this->h1["line_height"], $text);
		$this->Ln($this->h1["line_height"]);
	}

	function PrintH2($text, $text_color = 0x0) {
		$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
		$this->SetFont($this->default_font, $this->h2["style"], $this->h2["size"]);
		$this->Write($this->h2["line_height"], $text);
		$this->Ln($this->h2["line_height"]);
	}

	function PrintH3($text, $text_color = 0x0) {
		$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
		$this->SetFont($this->default_font, $this->h3["style"], $this->h3["size"]);
		$this->Write($this->h3["line_height"], $text);
		$this->Ln($this->h3["line_height"]);
	}

	function PrintParagraph($text, $text_color = 0x0) {
		$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
		$this->SetFont($this->default_font, $this->p["style"], $this->p["size"]);
		$this->Write($this->p["line_height"], $text);
		$this->Ln($this->p["line_height"]);
	}
	
	function PrintHR($width = 0.5, $length = 0, $align = "L", $color = 0x7f7f7f, $margin_top = 0, $margin_bottom = 0) {
		
		$page_width = $this->GetPageWidth() - ($this->margin * 2);
		
		// Fix the length of the line
		if ($length == 0 || $length > $page_width)
			$length = $page_width;
		
		$saved_y = $this->GetY();
		$yf = $this->GetY() + $margin_top;
		$yt = $yf;
		
		switch($align) {
			case "C" : {	// Centered
				$xf = $this->margin + (($page_width - $length) / 2);
				$xt = $xf + $length;
				break;
			}
			case "L" : {
				$xf = $this->margin;
				$xt = $xf + $length;
				break;
			}
			case "R": {
				$xf = $this->margin + $page_width - $length;
				$xt = $xf + $length;
			}
		}
		
		$this->SetLineWidth($width);
		$this->SetDrawColor(get_red($color), get_green($color), get_blue($color));
		$this->Line($xf, $yf, $xt, $yt);
		
		$this->SetXY($this->margin, $saved_y + $margin_top + $width + $margin_bottom);
	}
	
	function GetPrintAreaWidth() {
		return $this->GetPageWidth() - (2 * $this->margin);
	}
	
	function GetFontFamily() {
		return $this->default_font;
	}
	
	// Print one row of columns at the current row based on data specified in the array
	// The $columns array has the following structure
	//		*column_width - column_width in %
	// 		*content_type - "TEXT" or "IMAGE" or "MULTI"(for multi line text)
	// 		*content_value - File Name for IMAGE and Text for TEXT and array of the following structure for MULTI
	//							*text - Text to be printed
	//							align - "L" or "R" or "C" or "J" or """
	//							tag - h1, h2, h3, p or ""
	// 							font_size - in Points - defaults to 10
	//							font_color - hexadecimal number - defaults to 0x0
	//							font_style - "B" or "I" or "U" or "" - defaults to ""
	//							margin_left - default 0
	//							margin_right - default 0
	//							margin_top - default 0
	//							margin_bottom - default 0
	//		content_align = "L" or "R" or "C" coupled with "T" or "M" or "B" - default LT
	//		image_caption - Caption for Image
	//		image_link - Weblink
	//		image_print_width - Width of the image for printing (or 0)
	//		image_print_height - Height of the image for printing (or 0)
	// 		font_size - in Points
	//		font_color - hexadecimal number
	//		font_style - "B" or "I" or "U"
	function PrintColumns($columns, $margin_left = 0, $margin_right = 0, $margin_top = 0, $margin_bottom = 0, $spacing = 2, $width = 0) {
		// Pass 1 - Compute Output Specifications
		$x = $this->margin + $margin_left;
		$y = $this->GetY() + $margin_top;
		
		if ($width == 0 || $width > ($this->GetPrintAreaWidth() - $margin_left - $margin_right) )
			$width = round($this->GetPrintAreaWidth() - $margin_left - $margin_right, 1);
		
		$width_used = 0;
		$row_height = 0;
		$ospec = array();
		
		for ($i = 0; $i < sizeof($columns); ++$i) {
			
			$col = $columns[$i];
			
			// Default Values
			$content_align = ( isset($col['content_align']) && trim($col['content_align']) > "" ) ? $col['content_align'] : "LT";
			$image_caption = ( isset($col['image_caption']) && trim($col['image_caption']) > "" ) ? $col['image_caption'] : "";
			$image_caption_set = ($image_caption > "");
			$image_link = ( isset($col['image_link']) && trim($col['image_link']) > "" ) ? $col['image_link'] : "";
			$image_link_set = ($image_link > "");
			$image_print_width = ( isset($col['image_print_width']) && $col['image_print_width'] > 0 ) ? $col['image_print_width'] : 0;
			$image_print_width = ( isset($col['image_print_height']) && $col['image_print_height'] > 0 ) ? $col['image_print_height'] : 0;
			$font_size = ( isset($col['font_size']) && $col['font_size'] > 0 ) ? $col['font_size'] : 10;	// Default to font size of 10
			$font_color = ( isset($col['font_color']) && $col['font_color'] > 0 ) ? $col['font_color'] : 0x0;	// Default to Black
			$font_style = ( isset($col['font_style']) && ($col['font_style'] == "B" || $col['font_size'] == "I" || $col['font_size'] == "U") ) ? $col['font_style'] : "";	// Default to Regular style
			
			// Compute Column Widths
			$cellwidth = round(($width - ($spacing * (sizeof($columns) - 1))) * $col['column_width'] / 100, 1);
			
			// Protect last column width against any rounding errors
			if ($cellwidth > $width - $width_used)
				$cellwidth = round($width - $width_used, 1);
			
			// Compute Print Widths and adjust X & Y
			switch ($col['content_type']) {
				case "IMAGE" : {
					// Fix print width & height
					list ($img_width, $img_height) = getimagesize($col['content_value']);
					$aspect = $img_width / $img_height;
					// Convert picture size in pixels to Points
					$img_width = round($img_width / 300 * 72, 1);
					$img_height = round($img_height / 300 * 72, 1);

					// Defaults to physical size of the picture
					$print_width = $img_width;
					$print_height = $img_height;
					
					// If width is specified, resize the picture to that size
					if (isset($col['image_print_width']) && $col['image_print_width'] > 0) {
						// Resize if image is bigger
						if ($print_width > $col['image_print_width']) {
							$print_width = $col['image_print_width'];
							$print_height = round($print_width / $aspect, 1);
						}
					}
					
					if (isset($col['image_print_height']) && $col['image_print_height'] > 0) {
						// Resize if image is bigger
						if ($print_height > $col['image_print_height']) {
							$print_height = $col['image_print_height'];
							$print_width = round($print_height * $aspect, 1);
						}
					}
					
					// Limit to column width
					if ($print_width > $cellwidth) {
						$print_width = $cellwidth;
						$print_height = round($print_width / $aspect, 1);
					}
					
					// Compute space for caption
					$caption_height = 0;
					if ($image_caption_set) {
						$text = $image_caption;
						$this->SetFont($this->default_font, $font_style, $font_size);
						$lines = $this->WordWrap($text, $cellwidth);
						$caption_height = $lines * $font_size;
					}
					
					// Fix the Column Height
					$cellheight = $print_height + $caption_height;
					if ($row_height < $cellheight)
						$row_height = $cellheight;
					break;
				}
				case "TEXT" : {
					$text = trim($col['content_value']);
					$this->SetFont($this->default_font, $font_style, $font_size);
					$lines = $this->WordWrap($text, $cellwidth);
					$cellheight = $lines * $font_size;
					$print_width = $cellwidth;
					$print_height = $cellheight;
					if ($row_height < $cellheight)
						$row_height = $cellheight;
					break;
				}
				case "MULTI" : {
					$text_blocks = $col['content_value'];
					$content_value = array();
					$cellheight = 0;
					for ($idx = 0; $idx < sizeof($text_blocks); ++$idx) {
						$text_block = $text_blocks[$idx];
						$text_font_color = ( isset($text_block['font_color']) && $text_block['font_color'] > 0 ) ? $text_block['font_color'] : $font_color;	// Default to parent
						$text_align = ( isset($text_block['align']) && in_array($text_block['align'], array("L", "R", "C", "J")) ) ? $text_block['align'] : "L";
						if ( isset($text_block['tag']) && in_array(strtolower($text_block['tag']), array("h1", "h2", "h3", "p")) ) {
							switch (strtolower($text_block['tag'])) {
								case "h1" : {
									$text_font_size = $this->h1['size'];
									$text_fint_style = $this->h1['style'];
									break;
								}
								case "h2" : {
									$text_font_size = $this->h2['size'];
									$text_fint_style = $this->h2['style'];
									break;
								}
								case "h3" : {
									$text_font_size = $this->h3['size'];
									$text_fint_style = $this->h3['style'];
									break;
								}
								case "p" : {
									$text_font_size = $this->p['size'];
									$text_fint_style = $this->p['style'];
									break;
								}
								default : {
									$text_font_size = ( isset($text_block['font_size']) && $text_block['font_size'] > 0 ) ? $text_block['font_size'] : $font_size;	// Default to font size from the parent
									$text_font_style = ( isset($text_block['font_style']) && ($text_block['font_style'] == "B" || $text_block['font_size'] == "I" || $text_block['font_size'] == "U") ) ? $text_block['font_style'] : $font_style;	// Default to Regular style									
								}
							}
						}
						else {
							$text_font_size = ( isset($text_block['font_size']) && $text_block['font_size'] > 0 ) ? $text_block['font_size'] : $font_size;	// Default to font size from the parent
							$text_font_style = ( isset($text_block['font_style']) && ($text_block['font_style'] == "B" || $text_block['font_size'] == "I" || $text_block['font_size'] == "U") ) ? $text_block['font_style'] : $font_style;	// Default to Regular style
						}
						$text_margin_left = isset($text_block['margin_left']) ? $text_block['margin_left'] : 0;
						$text_margin_right = isset($text_block['margin_right']) ? $text_block['margin_right'] : 0;
						$text_margin_top = isset($text_block['margin_top']) ? $text_block['margin_top'] : 0;
						$text_margin_bottom = isset($text_block['margin_bottom']) ? $text_block['margin_bottom'] : 0;
						$text = trim($text_block['text']);
						$lines = $this->WordWrap($text, $cellwidth - $text_margin_left - $text_margin_right);
						$cellheight += ($lines * $text_font_size) + $text_margin_top + $text_margin_bottom;
						$content_value[] = array(
												"text" => $text_block['text'], "align" => $text_align, 
												"font_size" => $text_font_size, "font_color" => $text_font_color, "font_style" => $text_font_style,
												"margin_left" => $text_margin_left, "margin_right" => $text_margin_right, 
												"margin_top" => $text_margin_top, "margin_bottom" => $text_margin_bottom
												);
					}
					// debug_dump("multi_content_value", $content_value, __FILE__, __LINE__);
					if ($row_height < $cellheight)
						$row_height = $cellheight;
					break;
				}
			}
			$width_used += ($cellwidth + $spacing);
			
			$ospec[$i] = array("cell_width" => $cellwidth, "cell_height" => $row_height, 
							   "print_width" => $print_width, "print_height" => $print_height, "caption_height" => $caption_height,
							   "content_type" => $col['content_type'], "content_value" => ( $col['content_type'] == "MULTI" ? $content_value : $col['content_value']),
							   "content_align" => $content_align, 
							   "image_caption" => $image_caption, "image_caption_set" => $image_caption_set,
							   "image_link" => $image_link, "image_link_set" => $image_link_set,
							   "font_size" => $font_size, "font_color" => $font_color, "font_style" => $font_style
							);
		}
		
		// debug_dump("ospec", $ospec, __FILE__, __LINE__);
		
		// Pass 2 - Produce Output
		$saved_y = $this->GetY();				// For setting after the table is output
		$x = $this->margin + $margin_left;		// Start from left margin
		$y = $this->GetY() + $margin_top;		// Start from current vertical position on page
		
		for ($i = 0; $i < sizeof($ospec); ++$i) {
			$cell = $ospec[$i];
			$outx = $x;			// Set to current horizontal start position of the cell
			$outy = $y;			// Set to current vertical stat position of the cell
			
			switch ($cell['content_type']) {
				case "IMAGE" : {
					$halign = substr($cell['content_align'], 0, 1);
					$valign = substr($cell['content_align'], 1, 1);
					switch($halign) {
						case "L" : break;
						case "C" : $outx = $x + ($cell['cell_width'] - $cell['print_width']) / 2; break;
						case "R" : $outx = $x +  $cell['cell_width'] - $cell['print_width']; break;
						default : break;
					}
					switch($valign) {
						case "T" : break;
						case "M" : $outy = $y + ($cell['cell_height'] - $cell['print_height'] - $cell['caption_height']) / 2; break;
						case "B" : $outy = $y + $cell['cell_height'] - $cell['print_height'] - $cell['caption_height']; break;
						default : break;
					}
					// Output the image
					$this->SetXY($outx, $outy);
					$this->Image($cell['content_value'], $outx, $outy, $cell['print_width'], $cell['print_height'], "", $cell['image_link']);
					
					// Output Caption
					if ($cell['image_caption_set']) {
						$outx = $x;		// start of cell
						$outy += $cell['print_height'];
						$this->SetXY($outx, $outy);
						$text = $cell['image_caption'];
						$text_color = $cell['font_color'];
						$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
						$this->SetFont($this->default_font, $cell["font_style"], $cell["font_size"]);
						$this->WordWrap($text, $cell['cell_width']);
						$this->MultiCell($cell['cell_width'], $cell['font_size'], $text, 0, $halign);
					}
					break;
				}
				case "TEXT" : {
					$this->SetXY($outx, $outy);
					$text = $cell['content_value'];
					$text_color = $cell['font_color'];
					$this->SetTextColor(get_red($text_color), get_green($text_color), get_blue($text_color));
					$this->SetFont($this->default_font, $cell["font_style"], $cell["font_size"]);
					$this->WordWrap($text, $cell['cell_width']);
					$this->MultiCell($cell['cell_width'], $cell['font_size'], $text, 0, $halign);
					break;
				}
				case "MULTI" : {
					$text_blocks = $cell['content_value'];
					for ($idx = 0; $idx < sizeof($text_blocks); ++ $idx) {
						$text_block = $text_blocks[$idx];
						$text = $text_block['text'];
						$outx = $x + $text_block['margin_left'];
						$outy = $outy + $text_block['margin_top'];
						$this->SetXY($outx, $outy);
						$this->SetTextColor(get_red($text_block['font_color']), get_green($text_block['font_color']), get_blue($text_block['font_color']));
						$this->SetFont($this->default_font, $text_block["font_style"], $text_block["font_size"]);
						$lines = $this->WordWrap($text, $cell['cell_width'] - $text_block['margin_left'] - $text_block['margin_right']);
						$this->MultiCell($cell['cell_width'], $cell['font_size'], $text, 0, $text_block['align']);
						$outy += ($lines * $text_block['font_size']) + $text_block['margin_bottom'];	
					}
				}
			}
			$x += $cell['cell_width'] + $spacing;
		}
		// Set Current Position - x is set to left margin and y is set to current y + top margin + row height + bottom margin 
		$this->SetXY($this->margin, $saved_y + $margin_top + $row_height + $margin_bottom);
	}
}


?>