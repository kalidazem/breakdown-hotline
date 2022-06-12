<?php

namespace BreakdownHotline\Test\Unit\UltimateMemberExtend;

use BreakdownHotline\API\PostcodesAPI;
use BreakdownHotline\UltimateMemberExtend\UltimateMemberExtend;
use PHPUnit\Framework\TestCase;

class UltimateMemberExtendTest extends TestCase
{

    public function test_add_borough_to_search_query_args()
    {

        $postcodes_api = $this->get_postcodes_api_mock();
        $ultimate_member_extend = new UltimateMemberExtend($postcodes_api);
        $meta_query = ['meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'select_borough',
                'value' => 'GB.HU',
                'compare' => '='
            ],
            [
                'key' => 'select_borough',
                'value' => 'GB.HU',
                'compare' => 'like'
            ]
        ]];
        $search_query_args = $ultimate_member_extend->add_borough_to_search_query('GB.HU', $meta_query);
        $this->assertArrayHasKey('meta_query', $search_query_args);
    }

    public function test_get_borough_code_by_name_uppercase_and_lowercase()
    {
        $postcodes_api = $this->get_postcodes_api_mock();
        $ultimate_member_extend = new UltimateMemberExtend($postcodes_api);

        $list_of_boroughs = [
            'GB.HU' => 'Hounslow',
            'GB.BA' => 'Barking and Dagenham'
        ];

        //Upper case borough name
        $borough_code = $ultimate_member_extend->get_borough_code_by_name(
            $list_of_boroughs,
            'Hounslow'
        );

        $this->assertEquals('GB.HU', $borough_code);

        $borough_code = $ultimate_member_extend->get_borough_code_by_name(
            $list_of_boroughs,
            'hounslow'
        );

        $this->assertEquals('GB.HU', $borough_code);
    }

    /**
     * Creates a mock of the plugin Postcodes API class.
     *
     * @return void
     */
    private function get_postcodes_api_mock()
    {
        return $this->getMockBuilder(PostcodesAPI::class)->disableOriginalConstructor()->getMock();
    }
}
