<?php
// FilmzwithKanika — sends the website enquiry form to Kanika's inbox.
// To change where enquiries go, edit $to below.
$to   = 'kanika@filmzwithkanika.com';
$from = 'kanika@filmzwithkanika.com'; // an existing mailbox on this domain, so Hostinger delivers it

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: contact.html'); exit; }

// Spam trap: real visitors never fill this hidden field.
if (!empty($_POST['website'])) { header('Location: contact.html?sent=1'); exit; }

function field($key, $max = 200) {
    $v = trim(strip_tags($_POST[$key] ?? ''));
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr($v, 0, $max);
}

$name    = field('name');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$role    = field('role', 60);
$company = field('company');
$phone   = field('phone', 40);
$type    = field('type', 80);
$country = field('country', 80);
$budget  = field('budget');
$message = mb_substr(trim(strip_tags($_POST['message'] ?? '')), 0, 5000);

if ($name === '' || !$email) { header('Location: contact.html?error=1'); exit; }

$subject = 'New enquiry: ' . $name . ($type ? ' — ' . $type : '');
$body  = "New enquiry from filmzwithkanika.com\n\n";
$body .= "Name:      $name\n";
$body .= "Email:     $email\n";
$body .= "Phone:     $phone\n";
$body .= "I am a:    $role\n";
$body .= "Company:   $company\n";
$body .= "Wants:     $type\n";
$body .= "Country:   $country\n";
$body .= "Budget:    $budget\n\n";
$body .= "Message:\n$message\n";

$headers  = "From: FilmzwithKanika Website <$from>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers, '-f' . $from);
header('Location: contact.html?' . ($ok ? 'sent=1' : 'error=1'));
exit;
