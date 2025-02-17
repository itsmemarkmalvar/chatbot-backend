<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $ispBots = [
        'PLDT' => [
            'name' => 'PLDT AINA',
            'fullName' => 'Advanced Intelligent Network Assistant'
        ],
        'Globe' => [
            'name' => 'GlobeGuide',
            'fullName' => 'Your Digital Globe Assistant'
        ],
        'Converge' => [
            'name' => 'C-Verse',
            'fullName' => 'Your Virtual Converge Assistant'
        ]
    ];

    protected $ispData = [
        'PLDT' => [
            'plans' => [
                // Fiber Unli Plans
                [
                    'name' => 'Fiber Unli Plan 899',
                    'speed' => 35,
                    'price' => 899,
                    'type' => 'residential',
                    'features' => [
                        'Up to 35 Mbps Unlimited Fiber Internet',
                        'Suitable for web and social media browsing',
                        'Standard definition video streaming',
                        'Light file downloads',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                [
                    'name' => 'Fiber Unli Plan 1299',
                    'speed' => 100,
                    'price' => 1299,
                    'type' => 'residential',
                    'features' => [
                        'Up to 100 Mbps Unlimited Fiber Internet',
                        'Ideal for web surfing and email',
                        'Moderate web video streaming',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                [
                    'name' => 'Fiber Unli Plan 1699',
                    'speed' => 500,
                    'price' => 1699,
                    'type' => 'residential',
                    'features' => [
                        'Up to 500 Mbps Unlimited Fiber Internet',
                        'Great for heavy surfing',
                        'Video calls',
                        'Standard definition streaming on up to 3 devices',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                [
                    'name' => 'Fiber Unli Plan 2099',
                    'speed' => 700,
                    'price' => 2099,
                    'type' => 'residential',
                    'features' => [
                        'Up to 700 Mbps Unlimited Fiber Internet',
                        'Best for HD streaming',
                        'Large file transfers',
                        'Online gaming',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                [
                    'name' => 'Fiber Unli Plan 2699',
                    'speed' => 1000,
                    'price' => 2699,
                    'type' => 'residential',
                    'features' => [
                        'Up to 1 Gbps Unlimited Fiber Internet',
                        'Perfect for Ultra HD streaming',
                        'Online gaming on multiple devices',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                [
                    'name' => 'Fiber Unli Plan 9499',
                    'speed' => 1000,
                    'price' => 9499,
                    'type' => 'residential',
                    'features' => [
                        'Up to 1 Gbps Unlimited Fiber Internet',
                        'Designed for ultra-heavy internet users',
                        'Perfect for large households',
                        'Free Installation (6 months promo)',
                        'Free Activation (6 months promo)'
                    ]
                ],
                // Home Biz Plans
                [
                    'name' => 'Asenso Fiber Plan 1599',
                    'speed' => 100,
                    'price' => 1599,
                    'type' => 'business',
                    'features' => [
                        '100 Mbps Unlimited Fiber Internet',
                        'Unlimited calls to all mobile networks',
                        'Free access to e-commerce partner solutions',
                        'Ideal for growing businesses'
                    ]
                ],
                [
                    'name' => 'Asenso Fiber Plan 2099',
                    'speed' => 200,
                    'price' => 2099,
                    'type' => 'business',
                    'features' => [
                        '200 Mbps Unlimited Fiber Internet',
                        'Unlimited calls to all mobile networks',
                        'Free access to e-commerce partner solutions',
                        'Ideal for growing businesses'
                    ]
                ],
                [
                    'name' => 'Home Biz Fiber Plan 1999',
                    'speed' => 200,
                    'price' => 1999,
                    'type' => 'business',
                    'features' => [
                        '200 Mbps Unlimited Fiber Internet',
                        'Unlimited calls to all mobile networks',
                        'Unlimited calls to PLDT',
                        'Ideal for home-based businesses'
                    ]
                ],
                [
                    'name' => 'Home Biz Fiber Plan 2399',
                    'speed' => 300,
                    'price' => 2399,
                    'type' => 'business',
                    'features' => [
                        '300 Mbps Unlimited Fiber Internet',
                        'Unlimited calls to all mobile networks',
                        'Unlimited calls to PLDT',
                        'Ideal for home-based businesses'
                    ]
                ]
            ],
            'support_topics' => [
                'connection_issues' => [
                    'slow_connection' => [
                        'title' => 'Slow Internet Connection',
                        'steps' => [
                            'Restart your modem and router',
                            'Check for service outages in your area',
                            'Run a speed test at speedtest.net',
                            'Check connected devices for bandwidth usage',
                            'Contact PLDT support if issue persists'
                        ]
                    ],
                    'no_connection' => [
                        'title' => 'No Internet Connection',
                        'steps' => [
                            'Check all cable connections',
                            'Verify modem lights are normal',
                            'Restart your modem',
                            'Check for area outages',
                            'Contact technical support'
                        ]
                    ]
                ],
                'billing_info' => [
                    'payment_methods' => [
                        'Online Banking',
                        'PLDT App',
                        'GCash',
                        'Maya',
                        'Payment Centers',
                        'Auto-Debit'
                    ],
                    'billing_cycle' => 'Monthly billing starts from your installation date',
                    'due_date' => '7 days after bill delivery'
                ]
            ],
            'faqs' => [
                'categories' => [
                    [
                        'id' => 'internet_plans',
                        'title' => 'Internet Plans',
                        'questions' => [
                            'What are the available PLDT Fiber plans?',
                            'How do I upgrade my current plan?',
                            'What are the requirements for a new PLDT Fiber subscription?',
                            'Are there any ongoing promotions or discounts?'
                        ]
                    ],
                    [
                        'id' => 'technical_support',
                        'title' => 'Technical Support',
                        'questions' => [
                            'Why is my internet connection slow?',
                            'What should I do if I have no internet connection?',
                            'How do I reset my PLDT modem?',
                            'How can I check my internet speed?'
                        ]
                    ],
                    [
                        'id' => 'billing_payment',
                        'title' => 'Billing & Payment',
                        'questions' => [
                            'What are the available payment methods?',
                            'When is my monthly due date?',
                            'How do I view my PLDT bill online?',
                            'What should I do if my bill is incorrect?'
                        ]
                    ],
                    [
                        'id' => 'account_management',
                        'title' => 'Account Management',
                        'questions' => [
                            'How do I change my PLDT WiFi password?',
                            'How can I track my application status?',
                            'How do I report a service issue?',
                            'How can I update my account information?'
                        ]
                    ]
                ]
            ]
        ],
        'Globe' => [
            'plans' => [
                [
                    'name' => 'GFiber Plan 1499',
                    'speed' => 300,
                    'price' => 1499,
                    'features' => [
                        'Up to 300 Mbps Internet',
                        'WiFi 6 Modem supporting up to 15 devices with low latency',
                        'Disney+ Basic Annual Plan',
                        'Access to Blast TV',
                        'One-time installation fee of ₱2,400'
                    ]
                ],
                [
                    'name' => 'GFiber Plan 1999',
                    'speed' => 500,
                    'price' => 1999,
                    'features' => [
                        'Up to 500 Mbps Internet',
                        'WiFi 6 Modem supporting up to 15 devices with low latency',
                        'Disney+ Basic Annual Plan',
                        'Access to Blast TV',
                        'One-time installation fee of ₱2,400'
                    ]
                ],
                [
                    'name' => 'GFiber Plan 2799',
                    'speed' => 700,
                    'price' => 2799,
                    'features' => [
                        'Up to 700 Mbps Internet',
                        'WiFi 6 Modem supporting up to 40 devices with low latency',
                        'Disney+ Premium Annual Plan',
                        'Access to Blast TV',
                        '₱5,000 TP-Link Voucher',
                        'One-time installation fee of ₱2,400',
                        'HomeSquad Service (Free One-Time Visit)',
                        'Access to VIP Hotline'
                    ]
                ],
                [
                    'name' => 'GFiber Plan 4999',
                    'speed' => 1000,
                    'price' => 4999,
                    'features' => [
                        'Up to 1 Gbps Internet',
                        'WiFi 6 Modem supporting up to 50 devices with low latency',
                        'Disney+ Premium Annual Plan',
                        'Access to Blast TV',
                        '₱5,000 TP-Link Voucher',
                        'One-time installation fee of ₱2,400',
                        'HomeSquad Service (Free One-Time Visit)',
                        'Access to VIP Hotline'
                    ]
                ],
                [
                    'name' => 'GFiber Plan 7499',
                    'speed' => 1500,
                    'price' => 7499,
                    'features' => [
                        'Up to 1.5 Gbps Internet',
                        'WiFi 6 Modem supporting up to 70 devices with low latency',
                        'Disney+ Premium Annual Plan',
                        'Access to Blast TV',
                        '₱5,000 TP-Link Voucher',
                        'One-time installation fee of ₱2,400',
                        'HomeSquad Service (Free One-Time Visit)',
                        'Access to VIP Hotline'
                    ]
                ]
            ],
            'support_topics' => [
                'connection_issues' => [
                    'slow_connection' => [
                        'title' => 'Slow Internet Connection',
                        'steps' => [
                            'Power cycle your modem',
                            'Check Globe network status',
                            'Perform speed test',
                            'Monitor device usage',
                            'Contact Globe support'
                        ]
                    ],
                    'no_connection' => [
                        'title' => 'No Internet Connection',
                        'steps' => [
                            'Check physical connections',
                            'Verify modem status lights',
                            'Restart equipment',
                            'Check for maintenance schedules',
                            'Contact technical support'
                        ]
                    ]
                ],
                'billing_info' => [
                    'payment_methods' => [
                        'GCash',
                        'Globe One App',
                        'Online Banking',
                        'Credit Card',
                        'Payment Centers',
                        'Auto-Debit'
                    ],
                    'billing_cycle' => 'Monthly from activation date',
                    'due_date' => '7 days after bill generation'
                ]
            ],
            'faqs' => [
                'categories' => [
                    [
                        'id' => 'internet_plans',
                        'title' => 'Internet Plans',
                        'questions' => [
                            'What GFiber plans are available in my area?',
                            'How can I apply for a Globe At Home plan?',
                            'What are the requirements for Globe Fiber?',
                            'Are there any ongoing promos for new subscribers?'
                        ]
                    ],
                    [
                        'id' => 'technical_support',
                        'title' => 'Technical Support',
                        'questions' => [
                            'Why is my Globe internet not working?',
                            'How do I troubleshoot my Globe modem?',
                            'How can I improve my internet speed?',
                            'What do the modem lights mean?'
                        ]
                    ],
                    [
                        'id' => 'billing_payment',
                        'title' => 'Billing & Payment',
                        'questions' => [
                            'How can I pay my Globe internet bill?',
                            'When is my bill due?',
                            'How do I enroll in auto-debit?',
                            'Where can I view my billing statement?'
                        ]
                    ],
                    [
                        'id' => 'account_management',
                        'title' => 'Account Management',
                        'questions' => [
                            'How do I change my WiFi password?',
                            'How can I track my application?',
                            'How do I upgrade my plan?',
                            'How do I report a service issue?'
                        ]
                    ]
                ]
            ]
        ],
        'Converge' => [
            'plans' => [
                // Regular FiberX Plans
                [
                    'name' => 'FiberX Plan 1500',
                    'speed' => 35,
                    'price' => 1500,
                    'type' => 'residential',
                    'features' => [
                        'Unlimited Fiber Internet',
                        'Free Installation',
                        'Free Modem',
                        'No Data Caps',
                        '24/7 Customer Support'
                    ]
                ],
                [
                    'name' => 'FiberX Plan 2500',
                    'speed' => 100,
                    'price' => 2500,
                    'type' => 'residential',
                    'features' => [
                        'Unlimited Fiber Internet',
                        'Free Installation',
                        'Free Modem',
                        'No Data Caps',
                        '24/7 Customer Support'
                    ]
                ],
                [
                    'name' => 'FiberX Plan 3500',
                    'speed' => 200,
                    'price' => 3500,
                    'type' => 'residential',
                    'features' => [
                        'Unlimited Fiber Internet',
                        'Free Installation',
                        'Free Modem',
                        'No Data Caps',
                        '24/7 Customer Support'
                    ]
                ],
                // Time of Day Plans
                [
                    'name' => 'FiberX Day Plan 1899',
                    'speed_day' => 70,
                    'speed_night' => 35,
                    'price' => 1899,
                    'type' => 'time_of_day',
                    'features' => [
                        '70 Mbps (7:00 AM to 6:59 PM)',
                        '35 Mbps (7:00 PM to 6:59 AM)',
                        'Free Installation',
                        'Free Modem',
                        'No Data Caps'
                    ]
                ],
                [
                    'name' => 'FiberX Night Plan 1899',
                    'speed_day' => 35,
                    'speed_night' => 70,
                    'price' => 1899,
                    'type' => 'time_of_day',
                    'features' => [
                        '35 Mbps (7:00 AM to 6:59 PM)',
                        '70 Mbps (7:00 PM to 6:59 AM)',
                        'Free Installation',
                        'Free Modem',
                        'No Data Caps'
                    ]
                ],
                // FiberXtreme Plans
                [
                    'name' => 'FiberXtreme Plan 4500',
                    'speed' => 400,
                    'price' => 4500,
                    'type' => 'xtreme',
                    'features' => [
                        '400 Mbps Unlimited Fiber Internet',
                        'Free Installation',
                        'Free Advanced Modem',
                        'No Data Caps',
                        'Priority Customer Support'
                    ]
                ],
                [
                    'name' => 'FiberXtreme Plan 7000',
                    'speed' => 800,
                    'price' => 7000,
                    'type' => 'xtreme',
                    'features' => [
                        '800 Mbps Unlimited Fiber Internet',
                        'Free Installation',
                        'Free Advanced Modem',
                        'No Data Caps',
                        'Priority Customer Support'
                    ]
                ],
                // FlexiBIZ Daytime Plans
                [
                    'name' => 'FlexiBIZ Day 50',
                    'speed_peak' => 50,
                    'speed_offpeak' => 25,
                    'price' => 2000,
                    'type' => 'business',
                    'features' => [
                        '50 Mbps during peak hours (7:00 AM to 6:59 PM)',
                        '25 Mbps during non-peak hours',
                        'Business-grade Support',
                        'Static IP Available',
                        'Service Level Agreement'
                    ]
                ],
                [
                    'name' => 'FlexiBIZ Day 80',
                    'speed_peak' => 80,
                    'speed_offpeak' => 40,
                    'price' => 4000,
                    'type' => 'business',
                    'features' => [
                        '80 Mbps during peak hours (7:00 AM to 6:59 PM)',
                        '40 Mbps during non-peak hours',
                        'Business-grade Support',
                        'Static IP Available',
                        'Service Level Agreement'
                    ]
                ],
                // FlexiBIZ Peak Plans
                [
                    'name' => 'FlexiBIZ Peak 50',
                    'speed' => 50,
                    'price' => 3000,
                    'type' => 'business',
                    'features' => [
                        '50 Mbps Consistent Speed',
                        'Business-grade Support',
                        'Static IP Available',
                        'Service Level Agreement',
                        'Enterprise-level Security'
                    ]
                ],
                [
                    'name' => 'FlexiBIZ Peak 80',
                    'speed' => 80,
                    'price' => 6000,
                    'type' => 'business',
                    'features' => [
                        '80 Mbps Consistent Speed',
                        'Business-grade Support',
                        'Static IP Available',
                        'Service Level Agreement',
                        'Enterprise-level Security'
                    ]
                ]
            ],
            'support_topics' => [
                'connection_issues' => [
                    'slow_connection' => [
                        'title' => 'Slow Internet Connection',
                        'steps' => [
                            'Restart your modem',
                            'Check Converge service status',
                            'Conduct speed test',
                            'Review connected devices',
                            'Contact Converge support'
                        ]
                    ],
                    'no_connection' => [
                        'title' => 'No Internet Connection',
                        'steps' => [
                            'Verify fiber cable connection',
                            'Check modem indicators',
                            'Power cycle equipment',
                            'Check for service advisories',
                            'Contact technical support'
                        ]
                    ]
                ],
                'billing_info' => [
                    'payment_methods' => [
                        'GoFiber App',
                        'Online Banking',
                        'GCash',
                        'Maya',
                        'Payment Centers',
                        'Auto-Debit'
                    ],
                    'billing_cycle' => 'Monthly billing from installation',
                    'due_date' => '7 days from bill date'
                ]
            ],
            'faqs' => [
                'categories' => [
                    [
                        'id' => 'internet_plans',
                        'title' => 'Internet Plans',
                        'questions' => [
                            'What Converge FiberX plans are available?',
                            'How do I apply for Converge Fiber?',
                            'What are the installation requirements?',
                            'Are there any ongoing promotions?'
                        ]
                    ],
                    [
                        'id' => 'technical_support',
                        'title' => 'Technical Support',
                        'questions' => [
                            'Why is my Converge connection slow?',
                            'How do I reset my Converge modem?',
                            'What should I do if I have no internet?',
                            'How do I check my internet speed?'
                        ]
                    ],
                    [
                        'id' => 'billing_payment',
                        'title' => 'Billing & Payment',
                        'questions' => [
                            'What are the payment options for Converge?',
                            'When is my billing due date?',
                            'How do I view my bill online?',
                            'How do I dispute a billing issue?'
                        ]
                    ],
                    [
                        'id' => 'account_management',
                        'title' => 'Account Management',
                        'questions' => [
                            'How do I change my WiFi settings?',
                            'How can I track my application?',
                            'How do I report an issue?',
                            'How do I update my contact details?'
                        ]
                    ]
                ]
            ]
        ]
    ];

    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function processMessage($userId, $message, $provider, $category = null)
    {
        // Create initial chat record
        $chat = Chat::create([
            'user_id' => $userId,
            'provider' => $provider,
            'message' => $message,
            'category' => $category,
            'type' => 'text',
            'status' => 'processing'
        ]);

        try {
            // Process based on category
            if ($category) {
                switch ($category) {
                    case 'plans':
                        $response = $this->handlePlansQuery($provider);
                        break;
                    case 'support':
                        $response = $this->handleSupportQuery($message, $provider);
                        break;
                    case 'billing':
                        $response = $this->handleBillingQuery($message, $provider);
                        break;
                    case 'faqs':
                        $response = $this->handleFaqQuery($message, $provider);
                        break;
                    default:
                        $response = $this->processWithAI($message, $provider);
                }
            } else {
                $response = $this->processWithAI($message, $provider);
            }

            // Update chat record with response
            $chat->update([
                'response' => $response['message'],
                'type' => $response['type'] ?? 'text',
                'metadata' => $response['content'] ?? null,
                'status' => 'delivered'
            ]);

            return $chat->fresh();
        } catch (\Exception $e) {
            $chat->update(['status' => 'failed']);
            throw $e;
        }
    }

    protected function getBotName($provider) {
        return $this->ispBots[$provider]['name'] ?? 'ISP Support Assistant';
    }

    protected function formatResponse($message, $provider) {
        $botName = $this->getBotName($provider);
        // Remove any existing bot name prefix if present
        $message = preg_replace('/^\*\*[^*]+\*\*:\s*/', '', $message);
        return "**{$botName}**: {$message}";
    }

    protected function handlePlansQuery($provider)
    {
        try {
            Log::info('Handling plans query', [
                'provider' => $provider
            ]);

            if (!isset($this->ispData[$provider])) {
                Log::warning('Provider data not found', ['provider' => $provider]);
                return [
                    'type' => 'plans',
                    'message' => "Here are the available internet plans for {$provider}:",
                    'content' => null
                ];
            }

            $plans = $this->ispData[$provider]['plans'] ?? [];
            Log::info('Found plans', ['count' => count($plans)]);
            
            // Return the plans directly without additional formatting
            return [
                'type' => 'plans',
                'message' => "Here are the available internet plans for {$provider}:",
                'content' => [
                    'plans' => $plans
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error in handlePlansQuery', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function handleSupportQuery($message, $provider)
    {
        $supportTopics = $this->ispData[$provider]['support_topics'] ?? [];
        $response = $this->processWithAI($message, $provider);
        $botName = $this->getBotName($provider);

        if (isset($response['content']['steps'])) {
            $response['message'] = "Hi! I'm {$botName}. Here's how I can help you with that:";
        }

        return $response;
    }

    protected function handleBillingQuery($message, $provider)
    {
        try {
            Log::info('Handling billing query', [
                'provider' => $provider,
                'message' => $message
            ]);

            // Get user info from the auth service
            $userName = auth()->user()->name ?? null;

            // Get billing info from provider data
            $billingInfo = $this->ispData[$provider]['support_topics']['billing_info'] ?? null;

            // Create context with actual provider billing data and user name
            $context = "You are {$this->getBotName($provider)}, the billing specialist for {$provider}. 
            The user's name is {$userName}. Always address them by name in your greeting.
            
            Use this billing information in your response:
            - Payment Methods: " . implode(', ', $billingInfo['payment_methods'] ?? []) . "
            - Billing Cycle: {$billingInfo['billing_cycle']}
            - Due Date: {$billingInfo['due_date']}
            
            Start your response with a friendly greeting using their name.
            Then provide detailed information about billing processes, payment methods, and billing concerns.
            Make the response personal and professional.";

            $response = $this->geminiService->generateResponse($message, $provider, $context);
            
            return [
                'success' => true,
                'type' => 'text',
                'message' => $this->formatResponse($response['message'], $provider)
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleBillingQuery', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Get user name even in error case
            $userName = auth()->user()->name ?? 'there';
            
            return [
                'success' => false,
                'type' => 'text',
                'message' => $this->formatResponse(
                    "Hi {$userName}, I apologize, but I'm having trouble accessing the billing information at the moment. Please try again or contact our customer service for immediate assistance.",
                    $provider
                )
            ];
        }
    }

    protected function handleFaqQuery($message, $provider)
    {
        try {
            // If it's an initial FAQ request, return the categories and questions
            if (strtolower($message) === 'show me faqs for ' . strtolower($provider)) {
                Log::info('Processing FAQ request', [
                    'provider' => $provider,
                    'message' => $message
                ]);

                // Check if provider exists
                if (!isset($this->ispData[$provider])) {
                    Log::warning('Provider not found', ['provider' => $provider]);
                    return [
                        'success' => false,
                        'type' => 'text',
                        'message' => $this->formatResponse(
                            "I apologize, but I couldn't find FAQ information for {$provider}.",
                            $provider
                        )
                    ];
                }

                // Check if FAQs exist for provider
                if (!isset($this->ispData[$provider]['faqs']['categories'])) {
                    Log::warning('FAQs not found for provider', ['provider' => $provider]);
                    return [
                        'success' => false,
                        'type' => 'text',
                        'message' => $this->formatResponse(
                            "I apologize, but the FAQ information for {$provider} is currently unavailable.",
                            $provider
                        )
                    ];
                }

                $response = [
                    'success' => true,
                    'type' => 'faq_list',
                    'message' => "Here are the frequently asked questions for {$provider}:",
                    'content' => [
                        'categories' => $this->ispData[$provider]['faqs']['categories']
                    ]
                ];

                Log::info('FAQ response prepared', [
                    'response' => $response,
                    'categories_count' => count($this->ispData[$provider]['faqs']['categories'])
                ]);

                return $response;
            }

            // If it's a specific FAQ question, use AI to generate the response
            $context = "You are {$this->getBotName($provider)}, the AI assistant for {$provider}. 
            Provide a detailed, helpful answer to the following question about {$provider}'s services.
            Be specific and accurate in your response.
            
            Use this FAQ data for context:
            " . json_encode($this->ispData[$provider]['faqs'], JSON_PRETTY_PRINT);

            $response = $this->geminiService->generateResponse($message, $provider, $context);
            
            return [
                'success' => true,
                'type' => 'text',
                'message' => $this->formatResponse($response['message'], $provider)
            ];
        } catch (\Exception $e) {
            Log::error('Error in handleFaqQuery', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'type' => 'text',
                'message' => $this->formatResponse(
                    "I apologize, but I'm having trouble accessing the FAQ information at the moment. Please try again later.",
                    $provider
                )
            ];
        }
    }

    protected function processWithAI($message, $provider)
    {
        $response = $this->geminiService->generateResponse($message, $provider);
        
        // If the response is already formatted with the bot name, return as is
        if (strpos($response['message'], $this->getBotName($provider)) !== false) {
            return [
                'success' => true,
                'type' => 'text',
                'message' => $response['message']
            ];
        }
        
        // Otherwise, format the response with the bot name
        return [
            'success' => true,
            'type' => 'text',
            'message' => $this->formatResponse($response['message'], $provider)
        ];
    }
} 