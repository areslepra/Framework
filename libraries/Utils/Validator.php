<?php

namespace Roodaka\Framework\Utils;

class Validator Extends Component
 {
  public static function is_valid_email($email) {}
  public static function is_valid_userame($username) {}
  public static function is_valid_password($password) {}
  public static function is_correct_password($password, $stored) {}
  public static function is_valid_phone($phone) {}
  public static function is_valid_url($url) {}
  public static function is_valid_birthdate($day, $month, $year) {}
  public static function is_valid_sex($sex) {}
  public static function is_valid_ip($ip) {}
 }