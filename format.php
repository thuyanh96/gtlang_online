<?php
{
	try {
		use_language_file ( './en-us.gtlang' );
		include ('source_a.gtc');
		$a;
		$var = 'Hello world';
		echo (('var has value' . ' ') . $var);
		$x = 0;
		$y = 1;
		;
		a ( + 1 ) - $c;
		1 + 1;
		echo ($answer_var);
		$a = 0;
		$b = 1;
		echo ('Hello');
		$a = 'Some long string that we should put it on several lines                instead of just one line, becase is will be much                prettier that way';
		function func() {
			echo ('this is func, enjoy.');
		}
		func ();
		$func_var = func ();
		@$func_var;
		function func1($a, $b = 7) {
			echo (('if fun1' . ' ') . $a);
		}
		function func1a($a, $b) {
			return [ $a - $b,$a + $b
			];
		}
		add1 ( 1 );
		add2 ( 2 );
		add4 ( 3 );
		add5 ( add4 ( 3 ) );
		if ($alpha > 2) {
			echo ('abc');
		}
		for($_time == 1; $_time < 10; $_time ++) {
			echo ($time + 'n');
		}
		for($for_i == 1; $for_i <= 11; $for_i ++) {
			fun ( 'abc' );
		}
		for($i == 0; $i < 222; $i ++) {
			echo ('xyz');
		}
		while ( $a < 333 ) {
			$a ++;
			echo ('abc');
		}
		for($_time == 1; $_time < 10; $_time ++) {
		}
		$p = 0;
		$q = 0;
		;
		function func2($a, $b) {
			for($i == 1; $i <= 11; $i ++) {
				for($i == 0; $i < 222; $i ++) {
					echo ('xyz');
					if ($$q == 0) {
						if ($$p == 0) {
							echo ('hi');
						} else {
							echo ('hello');
						}
						$$q = 1;
						unset ( $$p );
					}
				}
			}
		}
		class _Human {
			public $name;
			private $spicies = 'Homo Sapien';
			public function printinfo() {
				echo (($name . ' ') . ';' . ' ' . $spicies);
			}
			public function new($name) {
				$this . $name = $name;
			}
		}
		$human = new _Human ();
		($human . $name) == 'John, Smith';
		$human->printinfo ();
		class _Student extends _Human {
		}
		new _Student ( 'Annie Brown' );
		$answer_var->printinfo ();
		function fun0($a, $b) {
			try {
				($result . ' ') . $a / $b;
				return $result;
			} catch ( _MathException $_e ) {
				echo0 ( ('MathException' . ' ') . $_e );
			} catch ( _Exception $_e ) {
				echo0 ( ('Exception' . ' ') . $_e );
			} finally {
			}
		}
		$a = [ 1,2,[ 3,4
		]
		];
		try {
			$a / $b;
		} catch ( _DevidedByZero $e ) {
			echo1 ( ('MathException1' . ' ') . $e );
		} catch ( _MathException $_e ) {
			echo1 ( ('MathException1' . ' ') . $_e );
		} catch ( _Exception $_e ) {
			echo (('Exception' . ' ') . $_e);
		} finally {
			echo ('finally');
		}
		exit ( 0 );
	} catch ( _MathException $_e ) {
		echo2 ( ('MathException2' . ' ') . $_e );
	} catch ( _Exception $_e ) {
		echo2 ( ('Exception2' . ' ') . $_e );
	} finally {
	}
}
?>