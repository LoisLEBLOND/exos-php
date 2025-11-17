<?php

function pgcd_1($a, $b) {
	$a = abs($a);
	$b = abs($b);
	while ($a != $b) {
		if ($a > $b) {
			$a = $a - $b;
		} else {
			$b = $b - $a;
		}
	}
	return $a;
}

function pgcd_2($a, $b) {
	$a = abs($a);
	$b = abs($b);
	while ($b != 0) {
		$r = $a % $b;
		$a = $b;
		$b = $r;
	}
	return $a;
}

function pgcd_3($a, $b) {
	$a = abs($a);
	$b = abs($b);
	if ($b == 0) {
		return $a;
	} else {
		return pgcd_3($b, $a % $b);
	}
}