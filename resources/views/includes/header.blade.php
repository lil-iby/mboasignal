<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>MboaSignal Dashboard</title>

  <!-- Google Fonts Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

  <!-- CSS commun dashboard -->
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
  <!-- Font Awesome pour les icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

</head>
<body>
