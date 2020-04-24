<?php

class TitleTailTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;

    public function setUp(): void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

    /**
     * `Rezept vegan` ---> `Rezept vegan`
     */
    public function test_one_word_stays()
    {
        $title = "Rezept vegan";
        $filtered_title = crv_filter_title_tail($title);
        $this->tester->assertEquals($title, $filtered_title);
    }

    /**
     * `Mega-cooles Rezept - roh & vegan` ---> `Mega-cooles Rezept`
     */
    public function test_two_words_get_cut()
    {
        $filtered_title = crv_filter_title_tail("Mega-cooles Rezept - roh & vegan");
        $this->tester->assertEquals("Mega-cooles Rezept", $filtered_title);
    }

    /**
     * `Mega-cooles Rezept &#45; roh &amp; vegan` ---> `Mega-cooles Rezept`
     */
    public function test_encoded_entities_get_cut()
    {
        $filtered_title = crv_filter_title_tail("Mega-cooles Rezept &#45; roh &amp; vegan");
        $this->tester->assertEquals("Mega-cooles Rezept", $filtered_title);
    }

    /**
     * `Mega-cooles roh-veganes Rezept` ---> `Mega-cooles roh-veganes Rezept`
     */
    public function test_stays_in_the_middle()
    {
        $title = "Mega-cooles roh-veganes Rezept";
        $filtered_title = crv_filter_title_tail($title);
        $this->tester->assertEquals($title, $filtered_title);
    }

    /**
     * `"Rezept" - roh & vegan` ---> `"Rezept"`
     */
    public function test_quotes_stay()
    {
        $filtered_title = crv_filter_title_tail('"Rezept" - roh & vegan');
        $this->tester->assertEquals('"Rezept"', $filtered_title);
    }

    /**
     * `&quot;Rezept&quot; - roh & vegan` ---> `&quot;Rezept&quot;`
     */
    public function test_enocoded_quotes_stay()
    {
        $filtered_title = crv_filter_title_tail('&quot;Rezept&quot; - roh & vegan');
        $this->tester->assertEquals('&quot;Rezept&quot;', $filtered_title);
    }

    /**
     * `Rezept (lecker) roh-vegan` ---> `Rezept (lecker)`
     */
    public function test_brackets_stay()
    {
        $filtered_title = crv_filter_title_tail("Rezept (lecker) roh-vegan");
        $this->tester->assertEquals("Rezept (lecker)", $filtered_title);
    }
}
