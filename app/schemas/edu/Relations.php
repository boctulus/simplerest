<?php 

return [
        'related_tables' => array (
  'categories' => 
  array (
    0 => 'courses',
  ),
  'course_details' => 
  array (
    0 => 'courses',
  ),
  'course_student' => 
  array (
    0 => 'courses',
    1 => 'users',
  ),
  'courses' => 
  array (
    0 => 'categories',
    1 => 'users',
    2 => 'course_details',
    3 => 'course_student',
  ),
  'users' => 
  array (
    0 => 'course_student',
    1 => 'courses',
  ),
),
        'relation_type'  => array (
  'categories~courses' => '1:n',
  'course_details~courses' => '1:1',
  'course_student~courses' => 'n:1',
  'course_student~users' => 'n:1',
  'courses~categories' => 'n:1',
  'courses~users' => 'n:m',
  'courses~course_details' => '1:1',
  'courses~course_student' => '1:n',
  'users~course_student' => '1:n',
  'users~courses' => 'n:m',
  'categories~users' => 'n:m',
  'users~categories' => 'n:m',
),
        'multiplicity'   => array (
  'categories~courses' => true,
  'course_details~courses' => false,
  'course_student~courses' => false,
  'course_student~users' => false,
  'courses~categories' => false,
  'courses~users' => true,
  'courses~course_details' => false,
  'courses~course_student' => true,
  'users~course_student' => true,
  'users~courses' => true,
  'categories~users' => true,
  'users~categories' => true,
),
];