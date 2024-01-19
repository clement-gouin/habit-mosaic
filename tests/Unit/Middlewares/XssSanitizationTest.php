<?php

namespace Tests\Unit\Middlewares;

use App\Http\Middleware\XssSanitization;
use Illuminate\Http\Request;
use Tests\TestCase;

class XssSanitizationTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider it_sanitizes_form_data_data
     */
    public function it_sanitizes_form_data(array $inputData, array $expectedOutput): void
    {
        $request = Request::create('/test', parameters: $inputData);

        (new XssSanitization())->handle($request, function (Request $outputRequest) use ($expectedOutput) {
            $this->assertEquals($expectedOutput, $outputRequest->all());

            return response()->noContent();
        });
    }

    public static function it_sanitizes_form_data_data(): array
    {
        return [
            'empty data' => [
                'inputData' => [],
                'expectedOutput' => [],
            ],
            'normal data' => [
                'inputData' => ['username' => 'myname', 'password' => '1234'],
                'expectedOutput' => ['username' => 'myname', 'password' => '1234'],
            ],
            'html data' => [
                'inputData' => ['username' => '<b>myname</b>', 'password' => '1234'],
                'expectedOutput' => ['username' => 'myname', 'password' => '1234'],
            ],
            //            'html ignored data' => [
            //                'inputData' => ['username' => 'myname', 'password' => '<test>'],
            //                'expectedOutput' => ['username' => 'myname', 'password' => '<test>'],
            //            ],
        ];
    }
}
