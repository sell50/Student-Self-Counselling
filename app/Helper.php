<?php

class Helper
{
    public static function term_available($mysqli, $term, $coursecode)
    {
        $numeric_code = explode("-", $coursecode);
        if ($numeric_code[1] == "4960A") {
            if ($numReqs = $mysqli->query("SELECT * FROM course_offerings WHERE course_id = 49601")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                if (in_array($term, $temp)) {
                    return 1;
                }
            }
            return 0;
        } else if ($numeric_code[1] == "4960B") {
            if ($numReqs = $mysqli->query("SELECT * FROM course_offerings WHERE course_id = 49602")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                if (in_array($term, $temp)) {
                    return 1;
                }
            }
            return 0;
        } else if ($numeric_code[1] == "4990A") {
            if ($numReqs = $mysqli->query("SELECT * FROM course_offerings WHERE course_id = 49901")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                if (in_array($term, $temp)) {
                    return 1;
                }
            }
            return 0;
        } else if ($numeric_code[1] == "4990B") {
            if ($numReqs = $mysqli->query("SELECT * FROM course_offerings WHERE course_id = 49902")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                if (in_array($term, $temp)) {
                    return 1;
                }
            }
            return 0;
        } else if ($numReqs = $mysqli->query("SELECT * FROM course_offerings WHERE course_id = " . $numeric_code[1])) { //query db for all rows in course_offerings
            $row = $numReqs->fetch_all(MYSQLI_NUM);
            $temp = flatten_array($mysqli, $row);
            if (in_array($term, $temp)) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public static function get_prereqs($mysqli, $term, $coursecode)
    { //have to pass $mysqli as an argument because we are using a global variable within a local scope
        $numeric_code = explode("-", $coursecode);
        if ($numeric_code[1] == "4960B") {
            if ($numReqs = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = 49602)")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                return $temp;
            }
            return 0;
        } else if ($numeric_code[1] == "4990B") {
            if ($numReqs = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = 49902)")) { //query db for all rows in course_offerings
                $row = $numReqs->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $row);
                return $temp;
            }
            return 0;
        } else if ($numReqs = $mysqli->query("SELECT num_requirements FROM course_requirements WHERE course_id = " . $numeric_code[1])) {
            $row = $numReqs->fetch_row();
            if ($row && $row[0] == 1 && $reqsList = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = " . $numeric_code[1] . ")")) {
                $reqsRow = $reqsList->fetch_row();
                return $reqsRow;
            } else if ($row && $row[0] == 2 && $reqsList = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 2_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req2 FROM 2_requirements WHERE course_id = " . $numeric_code[1] . ")")) {
                $reqsRow = $reqsList->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $reqsRow);
                return $temp;
            } else if ($row && $row[0] == 3 && $reqsList = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 3_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req2 FROM 3_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req3 FROM 3_requirements WHERE course_id = " . $numeric_code[1] . ")")) {
                $reqsRow = $reqsList->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $reqsRow);
                return $temp;
            } else if ($row && $row[0] == 4 && $reqsList = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 4_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req2 FROM 4_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req3 FROM 4_requirements WHERE course_id = " . $numeric_code[1] . ") OR id IN (SELECT req4 FROM 4_requirements WHERE course_id = " . $numeric_code[1] . ")")) {
                $reqsRow = $reqsList->fetch_all(MYSQLI_NUM);
                $temp = flatten_array($mysqli, $reqsRow);
                return $temp;
            } else { //return -1 if course has no prerequisites
                return -1;
            }
        } else {
            echo "query failed: " . $mysqli->error;
        }
    }

    public static function flatten_array($mysqli, $array)
    {
        $temp = array();
        for ($i = 0; $i < count($array); $i++) {
            for ($j = 0; $j < count($array[$i]); $j++) {
                $temp[] = $array[$i][$j];
            }
        }
        return $temp;
    }

    public static function increment_time(&$term, &$year)
    {
        if ($term == "Fall") { //switch to next term (we ignore summer)
            $term = "Winter";
        } else if ($term == "Winter") {
            $term = "Fall";
            if ($year == "First Year") {
                $year = "Second Year";
            } else if ($year == "Second Year") {
                $year = "Third Year";
            } else if ($year == "Third Year") {
                $year = "Fourth Year";
            }
        }
    }

    public static function substitute(string $course, int $program_id)
    {
		if(program_id == 1){
			if ($course === 'MATH-1250') {
				return 'MATH-1260';
			} else if ($course === 'MATH-1720') {
				return 'MATH-1760';
			} else if ($course === 'COMP-3340') {
				return 'COMP-3670';
			} else {
				return false;
			}
		}
		else if(program id == 2){
			if ($value == "MATH-1250") {
				return "MATH-1260";
			}
			else if ($value == "MATH-1720") {
				return "MATH-1760";
			}
			else if ($value == "MATH-3940") {
				return "MATH-3800";
			}
			else if ($value == "STAT-2910") {
				return "STAT-2920";
			}
			else if ($value == "COMP-4960A") {
				return "COMP-4990A";
			}
			else if ($value == "COMP-4960B") {
				return "COMP-4990B";
			}
			else {
				return false;
			}
		}
		else if(program id == 3){
			if ($value == "MATH-1250") {
				return "MATH-1260";
			} 
			else if ($value == "MATH-1720") {
				return "MATH-1760";
			} 
			else {
				return false;
			}
		}
		else if(program id == 4){
			if ($value == "MATH-1250") {
				return "MATH-1260";
			} 
			else if ($value == "MATH-1720") {
				return "MATH-1760";
			} 
			else {
				return false;
			}			
		}
		else if(program id == 5){
			if ($value == "MATH-1250") {
				return "MATH-1260";
			} 
			else if ($value == "MATH-1720") {
				return "MATH-1760";
			} 
			else if ($value == "STAT-2920") {
				return "STAT-2910";
			} 
			else if ($value == "COMP-4960A") {
				return "COMP-4990A";
			} 
			else if ($value == "COMP-4960B") {
				return "COMP-4990B";
			} 
			else {
				return false;
			}
		}
    }

    public static function flatten(array $array): array
    {
        $new = [];
        foreach ($array as $item) {
            $new[] = $item['code'];
        }
        return $new;
    }
}
