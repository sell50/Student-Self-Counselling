CREATE TABLE `programs`
(
    `id`   int unsigned auto_increment primary key,
    `name` varchar(100)
);

CREATE TABLE `courses`
(
    `id`      int unsigned auto_increment primary key,
    `code`    char(9),
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

CREATE TABLE `course_requirements`
(
    `course_id`        int(11) NOT NULL,
    `num_requirements` int(1) NOT NULL
);

CREATE TABLE `extra_courses`
(
    `id`          int(11) NOT NULL,
    `course_code` char(10),
    `credits`     int(11),
    `name`        varchar(100)
);

