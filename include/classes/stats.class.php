<?php
/**
 * Validator Class
 * 
 * @author Ryan NEl <rnel66@gmail.com>
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.0
 * @package Project
 */

# =========================================================================
# Stats CLASS
# =========================================================================

class Stats {
	# ---------------------------------------------------------------------
	# ATTRIBUTES
	# ---------------------------------------------------------------------
	
	# Public Attributes
	public $r;
	public $x;
	public $y;
	public $b;
	public $mean_x;
	public $mean_y;
	
	# Private Attributes
	private $sxy;
	private $sxx;
	private $syy;
	
	# ---------------------------------------------------------------------
	# FUNCTIONS
	# ---------------------------------------------------------------------
	function __construct($x, $y) {
		
		$this->x = $x;
		$this->y = $y;
		
		# Calculate Means
		$this->calculate_means();
		
		# Calculate Coeficient r
		$this->calculate_r();
		
		# Calculate B
		$this->calculate_b();	
	}
	
	function calculate_means() {
		$this->mean_x = $this->calculate_mean($this->x);
		$this->mean_y = $this->calculate_mean($this->y);
	}
	
	function calculate_mean($x) {
		$i = 0;
		$total = 0;
		foreach ($x as $xi) {
			$total = $total + $xi;
			$i++;
		}
		return ($i)? $total / $i : 0;
	}
	
	function calculate_r() {
		# Calculate Sxy
		for ($i = 0; $i < (sizeof($this->x) + 1); $i++) {
			$this->sxy = $this->sxy + (($this->x[$i] - $this->mean_x) * ($this->y[$i] - $this->mean_y));
		}
		
		# Calculate Sxx
		for ($i = 0; $i < (sizeof($this->x) + 1); $i++) {
			$this->sxx = $this->sxx + ($this->sqr($this->x[$i] - $this->mean_x));
		}
		
		# Calculate Syy
		for ($i = 0; $i < (sizeof($this->y) + 1); $i++) {
			$this->syy = $this->syy + ($this->sqr($this->y[$i] - $this->mean_y));
		}
		
		# Calculate r
		$this->r = $this->sxy / sqrt($this->sxx * $this->syy);
	}
	
	function calculate_b() {
		# Calculate b
		$this->b = $this->sxy / $this->sxx;
	}
	
	function sqr($x) {
		return $x * $x;
	}
	
	function linear_equation() {
		return "f(x) = y = {$this->mean_y} + {$this->b}x";
	}
}

?>