-- Database schema for Mctech-hub
-- Import this directly into your pre-created database online

-- Services Table
CREATE TABLE services (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  short_desc TEXT,
  full_desc TEXT,
  category ENUM('websites','apps','ai','care') NOT NULL,
  image VARCHAR(255),
  is_featured BOOLEAN DEFAULT FALSE,
  order_num INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects Table  
CREATE TABLE projects (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  short_desc TEXT,
  full_desc TEXT,
  client_type VARCHAR(100),
  outcome VARCHAR(255),
  image VARCHAR(255),
  service_id INT,
  is_featured BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Testimonials Table
CREATE TABLE testimonials (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_name VARCHAR(100) NOT NULL,
  company VARCHAR(100),
  message TEXT NOT NULL,
  rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
  image VARCHAR(255),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog Posts Table
CREATE TABLE blog_posts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  excerpt TEXT,
  content LONGTEXT,
  featured_image VARCHAR(255),
  author VARCHAR(100) DEFAULT 'Mctech-hub Team',
  is_published BOOLEAN DEFAULT FALSE,
  published_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contacts/Leads Table
CREATE TABLE contacts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20),
  message TEXT,
  service_interest VARCHAR(255),
  status ENUM('new','contacted','proposal','closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Users
CREATE TABLE admins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  role ENUM('admin','editor') DEFAULT 'admin',
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@mctech-hub.com');

-- FLAGship SERVICES (Your 4 core offers)
INSERT INTO services (name, slug, short_desc, full_desc, category, is_featured, order_num, image) VALUES
('Business Websites & Landing Pages', 'business-websites', 'Fast, mobile-ready sites for Uganda, Africa & global businesses', 'Professional websites that turn visitors into customers. Crafted in Uganda for companies across Africa and worldwide. Built with modern tech stack, SEO optimized, and conversion focused.', 'websites', TRUE, 1, 'services/websites.jpg'),
('Custom Web Apps & Portals', 'custom-web-apps', 'Secure web applications for African & global business processes', 'Portals, dashboards and internal tools that replace spreadsheets and manual work for SMEs worldwide. Fully dynamic, database-driven, with admin panels and mobile responsiveness.', 'apps', TRUE, 2, 'services/apps.jpg'),
('AI Voice & Chat Agents', 'ai-agents', '24/7 AI chatbots and voice agents for customer service', 'AI agents that handle inquiries, qualify leads and reduce support workload for businesses in Uganda, Africa and beyond. WhatsApp, website, and voice channel integration.', 'ai', TRUE, 3, 'services/ai-agents.jpg'),
('AI Workflow Automation & Care', 'ai-automation-care', 'AI automation plus website/systems maintenance', 'Automate operations and keep digital systems running smoothly with our Uganda-based care plans. AI-powered insights, security monitoring, and performance optimization.', 'care', TRUE, 4, 'services/care.jpg');

-- Sample Projects
INSERT INTO projects (title, slug, short_desc, full_desc, client_type, outcome, image, service_id, is_featured) VALUES
('Kampala International School Portal', 'kampala-school-portal', 'Online admissions and student management system', 'Complete web portal with online admissions, results publication, fee payments, parent communication, and admin dashboard. Deployed for leading Ugandan school with 2000+ students.', 'Education', '300% increase in online applications, 50% admin time reduction', 'projects/school.jpg', 2, TRUE),
('Nairobi Clinic AI Receptionist', 'nairobi-clinic-ai', 'WhatsApp & website AI agent for patient bookings', 'AI voice and chat agent handling appointments, patient inquiries, and prescription refills. Integrated with clinic management system. Serving 500+ patients monthly.', 'Healthcare', '50% reduction in admin calls, 24/7 availability', 'projects/clinic.jpg', 3, TRUE),
('Lagos SME Growth Website', 'lagos-sme-website', 'Conversion-focused business website', 'Modern 7-page website with lead capture forms, SEO optimization, and analytics integration. Delivered measurable lead growth for Nigerian manufacturing SME.', 'Business', '250% increase in qualified leads', 'projects/business.jpg', 1, TRUE),
('AI Automation for Agri-Tech', 'agri-tech-automation', 'Workflow automation for farm management', 'AI system automating crop reports, weather alerts, and supplier notifications for East African agribusiness. Processing 10,000+ data points daily.', 'Agriculture', '40% operational efficiency gain', 'projects/agri.jpg', 4, TRUE);

-- Testimonials
INSERT INTO testimonials (client_name, company, message, rating, image, is_active) VALUES
('Sarah M.', 'Kampala International School', 'The student portal transformed our admissions process. Parents love the mobile access and real-time updates. Mctech-hub delivered on time and trained our staff perfectly.', 5, 'testimonials/sarah.jpg', TRUE),
('Dr. James K.', 'Nairobi Medical Centre', 'The AI receptionist handles 80% of our routine calls. Patients get instant responses and our admin team focuses on complex cases. Exceptional value!', 5, 'testimonials/james.jpg', TRUE),
('Aisha N.', 'Lagos Manufacturing Ltd', 'Our new website generates steady leads daily. The team understood our industry and created exactly what we needed to compete regionally.', 5, 'testimonials/aisha.jpg', TRUE);

-- Blog Posts
INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, is_published, published_at) VALUES
('Why Ugandan Businesses Need AI Voice Agents in 2026', 'uganda-ai-voice-agents-2026', 'AI agents can handle 80% of customer inquiries automatically, available 24/7 across WhatsApp, website chat, and voice calls.', 'Long form content about AI benefits...', 'blog/ai-voice.jpg', TRUE, NOW()),
('5 Must-Have Features for African SME Websites', 'african-sme-websites-2026', 'Mobile-first design, WhatsApp integration, and fast loading are non-negotiable for businesses serving African customers.', 'Detailed website feature guide...', 'blog/sme-website.jpg', TRUE, NOW()),
('The Rise of Fintech in East Africa', 'fintech-east-africa', 'How mobile money and digital wallets are reshaping the financial landscape for small businesses in the region.', 'Content about fintech...', 'blog/fintech.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Cybersecurity Best Practices for Small Businesses', 'cybersecurity-tips', 'Protect your customer data and business reputation with these essential, low-cost security measures.', 'Content about security...', 'blog/security.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('How Mobile Money is Revolutionizing E-commerce', 'mobile-money-ecommerce', 'Integrating MTN MoMo and Airtel Money is no longer optional for online stores in Uganda.', 'Content about payments...', 'blog/mobile-money.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 7 DAY)),
('The Impact of 5G on African Tech Startups', '5g-impact-africa', 'Faster connectivity is unlocking new possibilities for IoT, remote work, and cloud computing across the continent.', 'Content about 5G...', 'blog/5g-tech.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Digital Marketing Strategies for 2026', 'digital-marketing-2026', 'Move beyond basic social media posts. Learn about programmatic ads, influencer partnerships, and data-driven growth.', 'Content about marketing...', 'blog/marketing.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Cloud Computing: A Game Changer for Enterprises', 'cloud-computing-africa', 'Why moving from on-premise servers to the cloud saves money and improves reliability for Ugandan companies.', 'Content about cloud...', 'blog/cloud.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('The Future of Remote Work in Uganda', 'remote-work-uganda', 'Tools and strategies for managing distributed teams effectively in a post-pandemic world.', 'Content about remote work...', 'blog/remote-work.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('Building Scalable Web Applications with PHP', 'scalable-php-apps', 'Technical insights on architecture, caching, and database optimization for high-traffic platforms.', 'Content about PHP...', 'blog/coding.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 20 DAY)),
('UX/UI Trends Dominating the African Market', 'ux-ui-trends-africa', 'Designing for accessibility, low-bandwidth environments, and local user behaviors.', 'Content about design...', 'blog/design.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Leveraging Data Analytics for Business Growth', 'data-analytics-growth', 'Turn your raw business data into actionable insights that drive revenue and reduce waste.', 'Content about data...', 'blog/data.jpg', TRUE, DATE_SUB(NOW(), INTERVAL 30 DAY));
