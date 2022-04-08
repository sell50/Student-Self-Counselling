CREATE TABLE `programs`
(
    `id`                 int unsigned auto_increment primary key,
    `name`               varchar(100),
    `total_courses`      tinyint unsigned,
    `additional_courses` tinyint unsigned,
    `art_courses`        tinyint unsigned,
    `social_courses`     tinyint unsigned,
    `art_social_courses` tinyint unsigned,
    `elective_courses`   tinyint unsigned
);

CREATE TABLE `courses`
(
    `id`      int unsigned auto_increment primary key,
    `code`    varchar(10),
    `name`    varchar(100),
    `credits` tinyint unsigned
);

CREATE TABLE `semesters`
(
    `id`   int unsigned auto_increment primary key,
    `name` varchar(10)
);

CREATE TABLE `course_program`
(
    `course_id`  int unsigned,
    `program_id` int unsigned,
    foreign key (course_id) references courses (id),
    foreign key (program_id) references programs (id)
);

CREATE TABLE `course_semester`
(
    `course_id`   int unsigned,
    `semester_id` int unsigned,
    foreign key (course_id) references courses (id),
    foreign key (semester_id) references semesters (id)
);

CREATE TABLE `course_prerequisite`
(
    `course_id`       int unsigned,
    `prerequisite_id` int unsigned,
    foreign key (course_id) references courses (id),
    foreign key (prerequisite_id) references courses (id)
);

CREATE TABLE `extra_courses`
(
    `id`          int(11) NOT NULL,
    `course_code` char(10),
    `credits`     int(11),
    `name`        varchar(100)
);

