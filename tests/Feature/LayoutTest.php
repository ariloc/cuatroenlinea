<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class Layout extends TestCase
{
    /**
     * Check layout and behaviour for different sequences.
     *
     * @return void
     */
    public function test_initial_board() {
		for ($i = 1; $i <= 7; $i++) {
			$response = $this->get("/jugar/$i");
			$dom = new Crawler($response->getContent());

			$board = $dom->filter('div.grid.grid-cols-7.grid-rows-6');
			$this->assertEquals(1, count($board));
			$this->assertEquals(41, count($board->filter('div.bg-gray-200')));

			$pos = 5 * 7 + $i;
			$this->assertEquals(1, count($board->filter("div.bg-red-500:nth-child($pos)")));

			$top_row = $dom->filter('div.grid.grid-cols-7.grid-rows-1');
			$this->assertEquals(1, count($top_row));
			$this->assertEquals(7, count($top_row
				->filter("a")
				->reduce(function (Crawler $node, $i) {
					$class_list = $node->attr('class');
					return substr_count($class_list, 'hover:bg-sky-500') == 1;
				})
			));
		}	
    }

	public function test_random_boards() {
		$boards = 20;
		while ($boards--) {
			$seq = $this->generate_random_valid_sequence();
			$response = $this->get("/jugar/$seq");
			$dom = new Crawler($response->getContent());

			$board = $dom->filter('div.grid.grid-cols-7.grid-rows-6');
			$this->assertEquals(1, count($board));
			$this->assertEquals(42 - strlen($seq), count($board->filter('div.bg-gray-200')));
			
			$this->assertEquals(floor(strlen($seq) / 2), count($board->filter('div.bg-sky-500')));
			$this->assertEquals(ceil(strlen($seq) / 2), count($board->filter('div.bg-red-500')));
			
			$top_row = $dom->filter('div.grid.grid-cols-7.grid-rows-1');
			$this->assertEquals(1, count($top_row));
			$this->assertEquals(7, count($top_row
				->filter("a")
				->reduce(function (Crawler $node, $i) use (&$seq) {
					$class_list = $node->attr('class');
					return substr_count($class_list, strlen($seq)%2 ? 'hover:bg-sky-500' : 'hover:bg-red-500') == 1;
				})
			));
		}
	}

	private function generate_random_valid_sequence() {
		$len = rand(1,42);

		$arr = [];
		for ($i = 1; $i <= 7; $i++)
			for ($j = 0; $j < 6; $j++)
				$arr[] = $i;

		$str = '';
		for ($i = 0; $i < $len; $i++) {
			$ind = rand(0,count($arr)-1);
			$str .= $arr[$ind] + '0';
			array_splice($arr, $ind, 1);
		}

		return $str;
	}
}
