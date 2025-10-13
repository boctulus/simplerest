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
  'course_tag' => 
  array (
    0 => 'courses',
    1 => 'tags',
  ),
  'courses' => 
  array (
    0 => 'categories',
    1 => 'users',
    2 => 'course_details',
    3 => 'course_student',
    4 => 'course_tag',
  ),
  'tags' => 
  array (
    0 => 'course_tag',
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
  'course_tag~courses' => 'n:1',
  'course_tag~tags' => 'n:1',
  'courses~categories' => 'n:1',
  'courses~users' => 'n:m',
  'courses~course_details' => '1:1',
  'courses~course_student' => '1:n',
  'courses~course_tag' => '1:n',
  'tags~course_tag' => '1:n',
  'users~course_student' => '1:n',
  'users~courses' => 'n:m',
  'categories~users' => 'n:m',
  'users~categories' => 'n:m',
  'courses~tags' => 'n:m',
  'tags~courses' => 'n:m',
),
        'multiplicity'   => array (
  'categories~courses' => true,
  'course_details~courses' => false,
  'course_student~courses' => false,
  'course_student~users' => false,
  'course_tag~courses' => false,
  'course_tag~tags' => false,
  'courses~categories' => false,
  'courses~users' => true,
  'courses~course_details' => false,
  'courses~course_student' => true,
  'courses~course_tag' => true,
  'tags~course_tag' => true,
  'users~course_student' => true,
  'users~courses' => true,
  'categories~users' => true,
  'users~categories' => true,
  'courses~tags' => true,
  'tags~courses' => true,
),
];