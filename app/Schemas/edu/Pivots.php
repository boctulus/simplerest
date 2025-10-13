<?php

$pivots        = array(
  'categories,users' => 'courses',
  'courses,users' => 'course_student',
  'courses,tags' => 'course_tag',
);

$pivot_fks     = array(
  'courses' =>
  array(
    'categories' => 'category_id',
    'users' => 'professor_id',
  ),
  'course_student' =>
  array(
    'courses' => 'course_id',
    'users' => 'user_id',
  ),
  'course_tag' =>
  array(
    'courses' => 'course_id',
    'tags' => 'tag_id',
  ),
);

$relationships = array(
  'courses' =>
  array(
    'categories' =>
    array(
      0 =>
      array(
        0 => 'categories.id',
        1 => 'courses.category_id',
      ),
    ),
    'users' =>
    array(
      0 =>
      array(
        0 => 'users.id',
        1 => 'courses.professor_id',
      ),
    ),
  ),
  'course_student' =>
  array(
    'courses' =>
    array(
      0 =>
      array(
        0 => 'courses.id',
        1 => 'course_student.course_id',
      ),
    ),
    'users' =>
    array(
      0 =>
      array(
        0 => 'users.id',
        1 => 'course_student.user_id',
      ),
    ),
  ),
  'course_tag' =>
  array(
    'courses' =>
    array(
      0 =>
      array(
        0 => 'courses.id',
        1 => 'course_tag.course_id',
      ),
    ),
    'tags' =>
    array(
      0 =>
      array(
        0 => 'tags.id',
        1 => 'course_tag.tag_id',
      ),
    ),
  ),
);
