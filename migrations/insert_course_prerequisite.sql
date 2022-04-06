INSERT INTO `course_prerequisite` (`course_id`, `prerequisite_id`)
VALUES ((select id from courses where code = 'MATH-1020'), (select id from courses where code = 'COMP-1000')),
       ((select id from courses where code = 'MATH-1730'), (select id from courses where code = 'MATH-1720')),
       ((select id from courses where code = 'COMP-2057'), (select id from courses where code = 'COMP-1400')),
       ((select id from courses where code = 'COMP-2120'), (select id from courses where code = 'COMP-1410')),
       ((select id from courses where code = 'COMP-2560'), (select id from courses where code = 'COMP-1410')),
       ((select id from courses where code = 'COMP-2650'), (select id from courses where code = 'COMP-1400')),
       ((select id from courses where code = 'COMP-2660'), (select id from courses where code = 'COMP-2650')),
       ((select id from courses where code = 'COMP-2707'), (select id from courses where code = 'COMP-2057')),
       ((select id from courses where code = 'COMP-2800'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3077'), (select id from courses where code = 'COMP-2707')),
       ((select id from courses where code = 'COMP-4220'), (select id from courses where code = 'COMP-3220')),
       ((select id from courses where code = 'COMP-4250'), (select id from courses where code = 'COMP-3150')),
       ((select id from courses where code = 'COMP-4670'), (select id from courses where code = 'COMP-3670')),
       ((select id from courses where code = 'COMP-4730'), (select id from courses where code = 'COMP-3710')),
       ((select id from courses where code = 'COMP-4740'), (select id from courses where code = 'COMP-3710')),
       ((select id from courses where code = 'COMP-4770'), (select id from courses where code = 'COMP-3770')),
       ((select id from courses where code = 'COMP-4960B'), (select id from courses where code = 'COMP-4960A')),
       ((select id from courses where code = 'COMP-4990B'), (select id from courses where code = 'COMP-4990A'));


INSERT INTO `course_prerequisite` (`course_id`, `prerequisite_id`)
VALUES ((select id from courses where code = 'COMP-1410'), (select id from courses where code = 'COMP-1000')),
       ((select id from courses where code = 'COMP-1410'), (select id from courses where code = 'COMP-1400')),

       ((select id from courses where code = 'COMP-2077'), (select id from courses where code = 'COMP-1047')),
       ((select id from courses where code = 'COMP-2077'), (select id from courses where code = 'COMP-2057')),

       ((select id from courses where code = 'COMP-2140'), (select id from courses where code = 'COMP-1000')),
       ((select id from courses where code = 'COMP-2140'), (select id from courses where code = 'COMP-2120')),

       ((select id from courses where code = 'COMP-2310'), (select id from courses where code = 'COMP-1000')),
       ((select id from courses where code = 'COMP-2310'), (select id from courses where code = 'MATH-1020')),

       ((select id from courses where code = 'COMP-2540'), (select id from courses where code = 'COMP-1000')),
       ((select id from courses where code = 'COMP-2540'), (select id from courses where code = 'COMP-1410')),

       ((select id from courses where code = 'COMP-3057'), (select id from courses where code = 'COMP-1047')),
       ((select id from courses where code = 'COMP-3057'), (select id from courses where code = 'COMP-2057')),

       ((select id from courses where code = 'COMP-3110'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3110'), (select id from courses where code = 'COMP-2540')),

       ((select id from courses where code = 'COMP-3150'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3150'), (select id from courses where code = 'COMP-2560')),

       ((select id from courses where code = 'COMP-3220'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3220'), (select id from courses where code = 'COMP-2540')),

       ((select id from courses where code = 'COMP-3340'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3340'), (select id from courses where code = 'COMP-2540')),

       ((select id from courses where code = 'COMP-3400'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3400'), (select id from courses where code = 'COMP-2560')),

       ((select id from courses where code = 'COMP-3500'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3500'), (select id from courses where code = 'COMP-2650')),

       ((select id from courses where code = 'COMP-3520'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3520'), (select id from courses where code = 'MATH-1250')),

       ((select id from courses where code = 'COMP-3680'), (select id from courses where code = 'COMP-3300')),
       ((select id from courses where code = 'COMP-3680'), (select id from courses where code = 'COMP-3670')),

       ((select id from courses where code = 'COMP-3710'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3710'), (select id from courses where code = 'STAT-2910')),

       ((select id from courses where code = 'COMP-3770'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3770'), (select id from courses where code = 'COMP-2120')),

       ((select id from courses where code = 'COMP-4110'), (select id from courses where code = 'COMP-3110')),
       ((select id from courses where code = 'COMP-4110'), (select id from courses where code = 'COMP-3300')),

       ((select id from courses where code = 'COMP-4150'), (select id from courses where code = 'COMP-3150')),
       ((select id from courses where code = 'COMP-4150'), (select id from courses where code = 'COMP-3300')),

       ((select id from courses where code = 'COMP-4200'), (select id from courses where code = 'COMP-3150')),
       ((select id from courses where code = 'COMP-4200'), (select id from courses where code = 'COMP-3220')),

       ((select id from courses where code = 'COMP-4680'), (select id from courses where code = 'COMP-3670')),
       ((select id from courses where code = 'COMP-4680'), (select id from courses where code = 'COMP-3680'));

INSERT INTO `course_prerequisite` (`course_id`, `prerequisite_id`)
VALUES ((select id from courses where code = 'COMP-3540'), (select id from courses where code = 'COMP-2140')),
       ((select id from courses where code = 'COMP-3540'), (select id from courses where code = 'COMP-2310')),
       ((select id from courses where code = 'COMP-3540'), (select id from courses where code = 'COMP-2540')),

       ((select id from courses where code = 'MATH-3940'), (select id from courses where code = 'COMP-1410')),
       ((select id from courses where code = 'MATH-3940'), (select id from courses where code = 'MATH-1730')),
       ((select id from courses where code = 'MATH-3940'), (select id from courses where code = 'MATH-1250')),

       ((select id from courses where code = 'COMP-4400'), (select id from courses where code = 'COMP-2140')),
       ((select id from courses where code = 'COMP-4400'), (select id from courses where code = 'COMP-2310')),
       ((select id from courses where code = 'COMP-4400'), (select id from courses where code = 'COMP-2540')),

       ((select id from courses where code = 'COMP-4540'), (select id from courses where code = 'COMP-2310')),
       ((select id from courses where code = 'COMP-4540'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-4540'), (select id from courses where code = 'COMP-3540')),

       ((select id from courses where code = 'COMP-4800'), (select id from courses where code = 'COMP-3110')),
       ((select id from courses where code = 'COMP-4800'), (select id from courses where code = 'COMP-3220')),
       ((select id from courses where code = 'COMP-4800'), (select id from courses where code = 'COMP-3300'));

INSERT INTO `course_prerequisite` (`course_id`, `prerequisite_id`)
VALUES ((select id from courses where code = 'COMP-3300'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3300'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3300'), (select id from courses where code = 'COMP-2560')),
       ((select id from courses where code = 'COMP-3300'), (select id from courses where code = 'COMP-2650')),

       ((select id from courses where code = 'COMP-3670'), (select id from courses where code = 'COMP-2120')),
       ((select id from courses where code = 'COMP-3670'), (select id from courses where code = 'COMP-2540')),
       ((select id from courses where code = 'COMP-3670'), (select id from courses where code = 'COMP-2560')),
       ((select id from courses where code = 'COMP-3670'), (select id from courses where code = 'COMP-2650'));