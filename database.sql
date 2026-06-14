-- Database Name: news_portal

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin: admin@example.com / password: password123
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`category_name`) VALUES
('Politics'),
('Sports'),
('International'),
('Technology'),
('Entertainment'),
('Education'),
('Business');

-- --------------------------------------------------------
-- Table structure for table `news`
-- --------------------------------------------------------
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `views` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample News Data
INSERT INTO `news` (`title`, `category_id`, `image`, `description`, `author`, `views`, `is_featured`) VALUES
('Global Summit Discusses Climate Change Action', 1, NULL, 'World leaders gathered today to discuss urgent measures to combat climate change and transition to renewable energy sources. The summit aims to reach a historic agreement on carbon emission reductions by 2030.', 'John Doe', 150, 1),
('New Tech Breakthrough in Quantum Computing', 4, NULL, 'Scientists have announced a major breakthrough in quantum computing that could revolutionize how we process data. This discovery paves the way for superfast calculations previously thought impossible.', 'Jane Smith', 85, 0),
('Championship Finals: Underdog Team Wins Big', 2, NULL, 'In a shocking turn of events, the underdog team secured a victory in last night\'s championship finals. Fans are celebrating across the city as the team brings home the trophy for the first time in history.', 'Mike Ross', 200, 0),
('Upcoming Blockbuster Movie Set for Summer Release', 5, NULL, 'The highly anticipated sci-fi thriller is finally hitting theaters this June. Directed by an Oscar winner, the film promises breathtaking visuals and a gripping storyline that will keep audiences on the edge of their seats.', 'Sarah Lee', 120, 0),
('Economic Growth Projected for the Next Quarter', 7, NULL, 'Financial analysts predict a steady rise in the stock market as major industries show signs of recovery. Consumer spending is up, and unemployment rates are at an all-time low, boosting investor confidence.', 'Robert Brown', 45, 0);

-- --------------------------------------------------------
-- Table structure for table `comments`
-- --------------------------------------------------------
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  CONSTRAINT `fk_news` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `comments` (`news_id`, `name`, `comment`) VALUES
(1, 'Alice', 'Great to see world leaders finally taking this seriously!'),
(1, 'Bob', 'We need action, not just words.');

-- --------------------------------------------------------
-- Table structure for table `ads`
-- --------------------------------------------------------
CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('header','sidebar','content') NOT NULL,
  `code` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ads` (`type`, `code`, `status`) VALUES
('sidebar', '<div class=\"bg-light border py-5 text-center text-muted\"><strong>Sidebar Ad Space</strong><br>300x250</div>', 1),
('content', '<div class=\"bg-light border py-4 text-center text-muted\"><strong>Content Banner Ad</strong><br>728x90</div>', 1);

COMMIT;
