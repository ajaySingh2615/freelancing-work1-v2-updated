<?php
/**
 * Get Locations API
 * Returns states by country or cities by state for dynamic dropdowns
 * MedStudy Global - Universities System
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Basic error handling
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function sendSuccess($data) {
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// Get request type and parameters
$type = $_GET['type'] ?? '';
$country = $_GET['country'] ?? '';
$state = $_GET['state'] ?? '';

// Validate request
if (empty($type)) {
    sendError('Missing type parameter');
}

// Location data arrays
$locations = [
    'countries' => [
        'Afghanistan', 'Albania', 'Algeria', 'Argentina', 'Armenia', 'Australia', 
        'Austria', 'Azerbaijan', 'Bahrain', 'Bangladesh', 'Belarus', 'Belgium', 
        'Bolivia', 'Bosnia and Herzegovina', 'Brazil', 'Bulgaria', 'Cambodia', 
        'Canada', 'Chile', 'China', 'Colombia', 'Croatia', 'Czech Republic', 
        'Denmark', 'Egypt', 'Estonia', 'Ethiopia', 'Finland', 'France', 'Georgia', 
        'Germany', 'Ghana', 'Greece', 'Hungary', 'Iceland', 'India', 'Indonesia', 
        'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Japan', 'Jordan', 'Kazakhstan', 
        'Kenya', 'Kuwait', 'Kyrgyzstan', 'Latvia', 'Lebanon', 'Lithuania', 
        'Luxembourg', 'Malaysia', 'Mexico', 'Morocco', 'Nepal', 'Netherlands', 
        'New Zealand', 'Nigeria', 'Norway', 'Oman', 'Pakistan', 'Philippines', 
        'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Saudi Arabia', 
        'Singapore', 'Slovakia', 'South Africa', 'South Korea', 'Spain', 'Sri Lanka', 
        'Sweden', 'Switzerland', 'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates', 
        'United Kingdom', 'United States', 'Uzbekistan', 'Vietnam'
    ],
    
    'states' => [
        'India' => [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 
            'Delhi', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 
            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 
            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 
            'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 
            'Uttarakhand', 'West Bengal'
        ],
        'United States' => [
            'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 
            'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 
            'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 
            'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 
            'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 
            'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 
            'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 
            'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 
            'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 
            'West Virginia', 'Wisconsin', 'Wyoming'
        ],
        'Canada' => [
            'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick', 
            'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia', 
            'Nunavut', 'Ontario', 'Prince Edward Island', 'Quebec', 'Saskatchewan', 
            'Yukon'
        ],
        'Australia' => [
            'Australian Capital Territory', 'New South Wales', 'Northern Territory', 
            'Queensland', 'South Australia', 'Tasmania', 'Victoria', 'Western Australia'
        ],
        'United Kingdom' => [
            'England', 'Scotland', 'Wales', 'Northern Ireland'
        ],
        'Germany' => [
            'Baden-Württemberg', 'Bavaria', 'Berlin', 'Brandenburg', 'Bremen', 
            'Hamburg', 'Hesse', 'Lower Saxony', 'Mecklenburg-Vorpommern', 
            'North Rhine-Westphalia', 'Rhineland-Palatinate', 'Saarland', 
            'Saxony', 'Saxony-Anhalt', 'Schleswig-Holstein', 'Thuringia'
        ],
        'Russia' => [
            'Moscow', 'Saint Petersburg', 'Novosibirsk Oblast', 'Yekaterinburg', 
            'Kazan', 'Nizhny Novgorod', 'Chelyabinsk', 'Samara', 'Omsk', 
            'Rostov-on-Don', 'Ufa', 'Krasnoyarsk', 'Perm', 'Voronezh', 'Volgograd'
        ]
    ],
    
    'cities' => [
        // India - Major states
        'Delhi' => ['New Delhi', 'Central Delhi', 'North Delhi', 'South Delhi', 'East Delhi', 'West Delhi'],
        'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati'],
        'Karnataka' => ['Bangalore', 'Mysore', 'Mangalore', 'Hubli', 'Belgaum', 'Davangere', 'Shimoga'],
        'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli'],
        'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar'],
        'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri'],
        'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Ghaziabad', 'Agra', 'Meerut', 'Varanasi', 'Allahabad'],
        'Rajasthan' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer', 'Bikaner'],
        'Punjab' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda'],
        'Haryana' => ['Gurgaon', 'Faridabad', 'Panipat', 'Ambala', 'Yamunanagar'],
        'Kerala' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam'],
        'Telangana' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar'],
        'Andhra Pradesh' => ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Tirupati'],
        
        // United States - Major states
        'California' => ['Los Angeles', 'San Francisco', 'San Diego', 'San Jose', 'Sacramento', 'Oakland'],
        'Texas' => ['Houston', 'Dallas', 'Austin', 'San Antonio', 'Fort Worth', 'El Paso'],
        'New York' => ['New York City', 'Buffalo', 'Rochester', 'Syracuse', 'Albany', 'Yonkers'],
        'Florida' => ['Miami', 'Tampa', 'Orlando', 'Jacksonville', 'Fort Lauderdale', 'Tallahassee'],
        'Illinois' => ['Chicago', 'Aurora', 'Joliet', 'Naperville', 'Peoria', 'Rockford'],
        
        // Canada - Major provinces
        'Ontario' => ['Toronto', 'Ottawa', 'Mississauga', 'Hamilton', 'London', 'Kitchener'],
        'Quebec' => ['Montreal', 'Quebec City', 'Laval', 'Gatineau', 'Longueuil', 'Sherbrooke'],
        'British Columbia' => ['Vancouver', 'Victoria', 'Surrey', 'Burnaby', 'Richmond', 'Abbotsford'],
        
        // Russia - Major regions
        'Moscow' => ['Moscow City', 'Zelenograd', 'Troitsk', 'Shcherbinka', 'Moskovsky'],
        'Saint Petersburg' => ['Saint Petersburg City', 'Kronstadt', 'Lomonosov', 'Pavlovsk'],
        'Kazan' => ['Kazan City', 'Naberezhnye Chelny', 'Nizhnekamsk', 'Almetyevsk'],
        
        // Default cities for countries without states
        'Germany' => ['Berlin', 'Hamburg', 'Munich', 'Cologne', 'Frankfurt', 'Stuttgart', 'Dortmund'],
        'France' => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg'],
        'Italy' => ['Rome', 'Milan', 'Naples', 'Turin', 'Palermo', 'Genoa', 'Bologna'],
        'Spain' => ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Málaga', 'Murcia'],
        'Netherlands' => ['Amsterdam', 'Rotterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen'],
        'Poland' => ['Warsaw', 'Krakow', 'Lodz', 'Wroclaw', 'Poznan', 'Gdansk', 'Szczecin'],
        'Ukraine' => ['Kiev', 'Kharkiv', 'Odessa', 'Dnipro', 'Donetsk', 'Zaporizhzhia', 'Lviv'],
        'Bangladesh' => ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal'],
        'Nepal' => ['Kathmandu', 'Pokhara', 'Lalitpur', 'Bharatpur', 'Biratnagar', 'Birgunj'],
        'China' => ['Beijing', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Tianjin', 'Wuhan', 'Chengdu'],
        'Philippines' => ['Manila', 'Cebu City', 'Davao City', 'Quezon City', 'Makati', 'Taguig'],
        'Kazakhstan' => ['Almaty', 'Nur-Sultan', 'Shymkent', 'Aktobe', 'Taraz', 'Pavlodar'],
        'Kyrgyzstan' => ['Bishkek', 'Osh', 'Jalal-Abad', 'Karakol', 'Tokmok', 'Uzgen'],
        'Georgia' => ['Tbilisi', 'Batumi', 'Kutaisi', 'Rustavi', 'Gori', 'Zugdidi'],
        'Armenia' => ['Yerevan', 'Gyumri', 'Vanadzor', 'Vagharshapat', 'Hrazdan', 'Abovyan']
    ]
];

switch ($type) {
    case 'countries':
        sendSuccess($locations['countries']);
        break;
        
    case 'states':
        if (empty($country)) {
            sendError('Country parameter required for states');
        }
        
        $states = $locations['states'][$country] ?? [];
        
        // If no specific states defined, return empty array (country uses cities directly)
        sendSuccess($states);
        break;
        
    case 'cities':
        if (empty($country)) {
            sendError('Country parameter required for cities');
        }
        
        $cities = [];
        
        // If state is provided, get cities by state
        if (!empty($state)) {
            $cities = $locations['cities'][$state] ?? [];
        } else {
            // If no state, get cities directly by country
            $cities = $locations['cities'][$country] ?? [];
        }
        
        sendSuccess($cities);
        break;
        
    default:
        sendError('Invalid type. Use: countries, states, or cities');
}
?> 