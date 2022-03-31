CREATE TABLE `majors` (
  `id` int(3) NOT NULL,
  `name` varchar(100)
);

CREATE TABLE `courses` (
  `id` int(11),
  `course_code` char(10),
  `credits` int(11),
  `name` varchar(100)
);

CREATE TABLE `major_requirements` (
  `major_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
);

CREATE TABLE `course_offerings` (
  `course_id` int(11) NOT NULL,
  `semester` varchar(10) NOT NULL
);

CREATE TABLE `course_requirements` (
  `course_id` int(11) NOT NULL,
  `num_requirements` int(1) NOT NULL
);

CREATE TABLE `extra_courses` (
  `id` int(11) NOT NULL,
  `course_code` char(10),
  `credits` int(11),
  `name` varchar(100)
);

