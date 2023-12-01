<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Herr Räf">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> 

        <title><?php echo(PAGE_TITLE); ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo(PAGE_STYLESHEET); ?>?id=<?php echo(rand(1,100)); ?>">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        
        <link rel="icon" href="graphics/dammsugare.png">
        
        <script src="<?php echo(PAGE_MAIN_JSDEFINES); ?>"></script>
        <script src="<?php echo(PAGE_MD5_ENCRYPT); ?>"></script>
        <script src="<?php echo(PAGE_MAIN_JSWINDOWSCROLL); ?>"></script>
        <script src="<?php echo(PAGE_FILESAVER); ?>"></script>
        
        <script src="<?php echo(VIEWCONTROLLER); ?>/controller.js"></script>
        <script src="<?php echo(USERCONTROLLER); ?>/controller.js"></script>
        <script src="<?php echo(INSIDECONTROLLER); ?>/controller.js"></script>
</head>
<body>
