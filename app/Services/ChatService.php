<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Http;

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
            ]
        ]
    ];

    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = new GeminiService($this->ispData);
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
                'metadata' => $response['metadata'] ?? null,
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
        $plans = $this->ispData[$provider]['plans'] ?? [];
        $botName = $this->getBotName($provider);
        
        return [
            'success' => true,
            'type' => 'plans',
            'message' => "Hello! I'm {$botName}. Here are the available plans for {$provider}:",
            'content' => [
                'plans' => $plans
            ]
        ];
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
        $context = "You are a billing specialist for {$provider}. 
        Provide detailed information about billing processes, payment methods, and common billing concerns. 
        Include information about payment deadlines, late fees, and available payment channels.";

        $prompt = "Based on this user query about {$provider} billing: '{$message}', 
        provide a helpful response that includes:
        1. Available payment methods
        2. Billing cycle information
        3. Common billing FAQs
        4. Contact information for billing support
        Make sure to format the response in a clear, organized manner.";

        return $this->geminiService->generateResponse($prompt, $context);
    }

    protected function handleFaqQuery($message, $provider)
    {
        // Get AI response for FAQs
        $context = [
            'query_type' => 'faq',
            'provider' => $provider
        ];
        
        $response = $this->geminiService->generateResponse($message, $provider, $context);
        
        return [
            'message' => $response['message'],
            'type' => 'text'
        ];
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