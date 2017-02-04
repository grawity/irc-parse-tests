<?php
namespace IRC;

function _substr($str, $start) {
	$ret = substr($str, $start);
	return $ret === false ? "" : $ret;
}

class Line {
	public $tags = array();
	public $prefix = null;
	public $verb = null;
	public $args = array();

	static function split($line) {
		$line = rtrim($line, "\r\n");
		$line = explode(" ", $line);
		$i = 0; $n = count($line);
		$parv = array();

		while ($i < $n && $line[$i] === "")
			$i++;

		if ($i < $n && $line[$i][0] == "@") {
			$parv[] = $line[$i];
			$i++;
			while ($i < $n && $line[$i] === "")
				$i++;
		}

		if ($i < $n && $line[$i][0] == ":") {
			$parv[] = $line[$i];
			$i++;
			while ($i < $n && $line[$i] === "")
				$i++;
		}

		while ($i < $n) {
			if ($line[$i] === "")
				;
			elseif ($line[$i][0] === ":")
				break;
			else
				$parv[] = $line[$i];
			$i++;
		}

		if ($i < $n) {
			$trailing = implode(" ", array_slice($line, $i));
			$parv[] = _substr($trailing, 1);
		}

		return $parv;
	}

	static function parse($line) {
		$parv = self::split($line);
		$i = 0; $n = count($parv);
		$self = new self();

		if ($i < $n && $parv[$i][0] === "@") {
			$tags = _substr($parv[$i], 1);
			$i++;
			foreach (explode(";", $tags) as $item) {
				list($k, $v) = explode("=", $item, 2);
				if ($v === null)
					$self->tags[$k] = true;
				else
					$self->tags[$k] = $v;
			}
		}

		if ($i < $n && $parv[$i][0] === ":") {
			$self->prefix = _substr($parv[$i], 1);
			$i++;
		}

		if ($i < $n) {
			$self->verb = strtoupper($parv[$i]);
			$self->args = array_slice($parv, $i);
		}

		return $self;
	}

	static function join($argv) {
		$i = 0; $n = count($argv);

		if ($i < $n && $argv[$i][0] == "@") {
			if (strpos($argv[$i], " ") !== false)
				return null;
			$i++;
		}

		if ($i < $n && strpos($argv[$i], " ") !== false) {
			return null;
		}

		if ($i < $n && $argv[$i][0] == ":") {
			if (strpos($argv[$i], " ") !== false)
				return null;
			$i++;
		}

		while ($i < $n-1) {
			if (!strlen($argv[$i]) || $argv[$i][0] == ":"
			    || strpos($argv[$i], " ") !== false) {
				return null;
			}
			$i++;
		}

		$parv = array_slice($argv, 0, $i);

		if ($i < $n) {
			if (!strlen($argv[$i]) || $argv[$i][0] == ":"
			    || strpos($argv[$i], " ") !== false) {
				$parv[] = ":".$argv[$i];
			} else {
				$parv[] = $argv[$i];
			}
		}

		return implode(" ", $parv);
	}
}
