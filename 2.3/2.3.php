
<?php

function my_str_contains($haystack, $needle) {
	$haystack_len = strlen($haystack);
	$needle_len = strlen($needle);
	if ($needle_len === 0) {
		return true;
	}
	if ($needle_len > $haystack_len) {
		return false;
	}
	for ($i = 0; $i <= $haystack_len - $needle_len; $i++) {
		$found = true;
		for ($j = 0; $j < $needle_len; $j++) {
			if (!isset($haystack[$i + $j]) || $haystack[$i + $j] !== $needle[$j]) {
				$found = false;
				break;
			}
		}
		if ($found) {
			return true;
		}
	}
	return false;
}
