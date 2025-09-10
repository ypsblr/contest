<html>
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
<hr><br><br>
<form action="zipmaker.php" method="post"
        enctype="multipart/form-data" class="form-inline">

		
  <div class="form-group">
    <label for="email">Excel Sheet:</label>
	 <input type="file" name="file" id="file">
  </div>
  <button type="submit" name="submit" class="btn btn-primary">Upload</button>


</form>
</div>

</body>
</html>