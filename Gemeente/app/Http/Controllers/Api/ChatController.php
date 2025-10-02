<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use App\Services\PrivacyLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle chatbot conversation
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'session_id' => 'nullable|string|max:100',
        ]);

        $message = $request->input('message');
        $sessionId = $request->input('session_id', session()->getId());

        try {
            $response = $this->chatbotService->processMessage($message);

            // Log conversation for analytics (privacy-safe)
            PrivacyLogger::logUserAction('chatbot_interaction', [
                'session_hash' => substr(hash('sha256', $sessionId), 0, 16),
                'message_length' => strlen($message),
                'response_type' => $response['type'] ?? 'unknown',
                'intent_detected' => $this->extractIntent($response),
            ]);

            return response()->json([
                'success' => true,
                'response' => $response,
                'session_id' => $sessionId,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            PrivacyLogger::logSecurityEvent('chatbot_error', [
                'error_type' => get_class($e),
                'message_length' => strlen($message),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Er is een fout opgetreden. Probeer het opnieuw of neem contact op.',
                'fallback_response' => [
                    'type' => 'error',
                    'message' => "Sorry, er ging iets mis! 😕\n\nU kunt altijd:\n📞 Bellen naar 14 020\n🌐 Onze website bezoeken\n📧 Een email sturen",
                    'quick_replies' => ['Contact opnemen', 'Website bezoeken'],
                ],
            ], 500);
        }
    }

    /**
     * Get chatbot introduction/welcome message
     */
    public function welcome(): JsonResponse
    {
        $welcomeResponse = [
            'type' => 'welcome',
            'message' => "Welkom bij de gemeente chatbot! 🏛️\n\n".
                        "Ik kan u helpen met:\n".
                        "📋 Klachten indienen en status opzoeken\n".
                        "🔍 Uw klacht-ID terugvinden\n".
                        "📞 Contact informatie en openingstijden\n".
                        "ℹ️ Algemene vragen over gemeente diensten\n\n".
                        'Waarmee kan ik u vandaag helpen?',
            'quick_replies' => [
                'Klacht indienen',
                'Status opzoeken',
                'Waar vind ik mijn klacht-ID?',
                'Contact informatie',
            ],
        ];

        return response()->json([
            'success' => true,
            'response' => $welcomeResponse,
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get frequently asked questions
     */
    public function faq(): JsonResponse
    {
        $faqResponse = [
            'type' => 'faq',
            'message' => "Veelgestelde vragen: ❓\n\n".
                        "**Klacht gerelateerd:**\n".
                        "• Hoe dien ik een klacht in?\n".
                        "• Waar vind ik mijn klacht-ID?\n".
                        "• Wat betekenen de verschillende statussen?\n".
                        "• Hoe lang duurt behandeling?\n\n".
                        "**Contact & Service:**\n".
                        "• Wat zijn de openingstijden?\n".
                        "• Hoe kan ik contact opnemen?\n".
                        "• Waar vind ik het gemeentehuis?\n".
                        '• Welke diensten zijn online beschikbaar?',
            'quick_replies' => [
                'Klacht indienen uitleg',
                'Status betekenis',
                'Contact informatie',
                'Online diensten',
            ],
        ];

        return response()->json([
            'success' => true,
            'response' => $faqResponse,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Extract intent from chatbot response for logging
     */
    private function extractIntent(array $response): string
    {
        return $response['type'] ?? 'unknown';
    }
}
