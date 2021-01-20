<?php
namespace Codebykyle\CalculatedField\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\NovaServiceProvider;

class CalculatedFieldController extends Controller
{
    public function calculate($resource, $field, NovaRequest $request)
    {

        app()->register(NovaServiceProvider::class);

        $field = $request->newResource()
            ->availableFields($request)
            ->firstWhere('attribute', '=', $field);

        if (empty($field)) {
            abort(404, "Unable to find the field required to calculate this value");
        }

        $calculatedValue = call_user_func(
            $field->calculateFunction,
            collect($request->json()->all()['values']),
            $request
        );

        return response()->json([
            'value' => $calculatedValue,
        ]);
    }
}
