<?php

namespace BreakdownHotline\Tests\Integration\UltimateMemberExtend;

use BreakdownHotline\API\PostcodesAPI;
use BreakdownHotline\UltimateMemberExtend\UltimateMemberExtend;
use PHPUnit\Framework\TestCase;

class UltimateMemberExtendTest extends TestCase
{
    public function test_um_prepare_user_lookup_custom_query()
    {
        $_POST['search'] = 'tw47pl';
        $postcodes_api = $this->get_postcodes_api();
        $ultimate_member_extend = new UltimateMemberExtend($postcodes_api);

        //mock query args, it does not matter what um has added to $query_args
        //we are only concerned with the $query_args['meta_query']
        $query_args = [];
        $query_args = $ultimate_member_extend->um_prepare_user_lookup_custom_query(
            $query_args,
            [
                'form_id' => 285
            ]
        );
        $this->assertArrayHasKey('meta_query', $query_args);
        $this->assertEquals('OR', $query_args['meta_query']['relation']);
        foreach ($query_args['meta_query'] as $key => $value) {
            if (is_array($value)) {
                $this->assertEquals($query_args['meta_query'][$key]['key'], 'select_borough');
                $this->assertEquals(
                    $query_args['meta_query'][$key]['value'],
                    'GB.HU'
                ); // as I know it belongs to tw47pl which was passed via $_POST['search'] = 'tw47pl'
                $this->assertThat(
                    $query_args['meta_query'][$key]['compare'],
                    $this->logicalOr($this->equalTo('='), $this->equalTo('LIKE'))
                );
            }
        }
    }

    private function get_postcodes_api()
    {
        $http_transport = new \WP_Http();
        return new PostcodesAPI($http_transport);
    }
}
