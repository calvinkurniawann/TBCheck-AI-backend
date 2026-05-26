<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ScreeningHistory;
use App\Services\AiAdviceService;

class TbScreeningController extends Controller
{
    private array $cfWeights = [
        'G01' => 0.8,
        'G02' => 0.6,
        'G03' => 0.8,
        'G04' => 0.8,
        'G05' => 0.8,
        'G06' => 0.6,
        'G07' => 0.6,
        'G08' => 0.6,
        'R01' => 0.6,
        'R02' => 0.4,
        'R03' => 0.4,
        'R04' => 0.4,
        'R05' => 0.4,
        'K01' => 0.6,
        'K02' => 0.4,
        'K03' => 0.4,
        'K04' => 0.4,
    ];

    private AiAdviceService $aiService;

    public function __construct(AiAdviceService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function calculateRisk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'selected_symptoms' => 'required|array',
            'selected_symptoms.*' => 'string'
        ]);

        $selectedSymptoms = $validated['selected_symptoms'];
        $cfCombine = 0.0;

        foreach ($selectedSymptoms as $symptom) {
            if (isset($this->cfWeights[$symptom])) {
                $cfExpert = $this->cfWeights[$symptom];
                
                if ($cfCombine === 0.0) {
                    $cfCombine = $cfExpert;
                } else {
                    $cfCombine = $cfCombine + $cfExpert * (1 - $cfCombine);
                }
            }
        }

        $riskLevel = 'Rendah';
        
        if ($cfCombine >= 0.7) {
            $riskLevel = 'Tinggi';
        } elseif ($cfCombine >= 0.4) {
            $riskLevel = 'Sedang';
        }

        $aiAdvice = $this->aiService->generateAdvice($riskLevel, $selectedSymptoms);

        // Use authenticated user's ID
        ScreeningHistory::create([
            'user_id' => $request->user()->id,
            'selected_symptoms' => $selectedSymptoms,
            'cf_score_raw' => $cfCombine,
            'cf_score_percentage' => round($cfCombine * 100, 2),
            'risk_level' => $riskLevel,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'cf_score_raw' => $cfCombine,
                'cf_score_percentage' => round($cfCombine * 100, 2),
                'risk_level' => $riskLevel,
                'ai_advice' => $aiAdvice,
            ]
        ], 200);
    }

    public function getHistory(Request $request): JsonResponse
    {
        // Use authenticated user's ID
        $histories = ScreeningHistory::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $histories->map(function ($h) {
                return [
                    'id' => $h->id,
                    'selected_symptoms' => $h->selected_symptoms,
                    'cf_score_raw' => $h->cf_score_raw,
                    'cf_score_percentage' => $h->cf_score_percentage,
                    'risk_level' => $h->risk_level,
                    'created_at' => $h->created_at->toIso8601String(),
                ];
            }),
        ], 200);
    }
}