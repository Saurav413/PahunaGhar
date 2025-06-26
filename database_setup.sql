-- Create database
CREATE DATABASE IF NOT EXISTS pahunaghar;
USE pahunaghar;

-- Create hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price VARCHAR(50) NOT NULL,
    rating DECIMAL(3,1) DEFAULT 0.0,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample hotel data
INSERT INTO hotels (name, location, description, price, rating, image_url) VALUES
('The Soaltee Kathmandu', 'Kathmandu', 'A luxurious hotel in the heart of the city with world-class amenities and stunning views.', '$300/night', 4.8, 'https://soalteehotel.com/uploads/images/20250115105121_67879329ecc905.93783695.jpg'),
('Hyatt Place Kathmandu', 'Kathmandu', 'Start here. Discover everywhere.', '$103/night', 4.5, 'https://assets.hyatt.com/content/dam/hyatt/hyattdam/images/2025/01/26/0103/KTMZK-P0132-Building-Exterior.jpg/KTMZK-P0132-Building-Exterior.16x9.jpg'),
('Gurung Cottage', 'Pokhara', 'A cozy retreat with breathtaking mountain views and personalized service.', '$36.53/night', 4.7, 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/03/d7/a6/8f/hotel-gurung-cottage.jpg?w=1800&h=1000&s=1'),
('The Everest Hotel, Kathmandu', 'Kathmandu', 'Modern suites in the city center, perfect for business and leisure travelers alike.', '$160/night', 4.6, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusyajFAvsL_e2vtgIWle9ASijkLldLXxciw&s'),
('Sarangkot Mountain Lodge', 'Pokhara', 'Relax by the lake and enjoy tranquil surroundings at this beautiful getaway.', '$195/night', 4.9, 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2e/87/87/68/sarangkot-mountain-lodge.jpg?w=1400&h=800&s=1'),
('Hotel Ichchha', 'Kathmandu', 'Experience the charm of history with modern comforts in the heart of downtown.', '$150/night', 4.4, 'https://hotelichchha.com/backend/images/slideshow/FLgo3-importbanner.webp'),
('HOTEL VISHUWA', 'Chitwan', 'Hotel Vishuwa is a three-star luxury hotel situated in the Terai plains of Nepal', '$50/night', 4.3, 'https://www.vishuwa.com/wp-content/uploads/2018/01/DSC4788-copy.jpg'),
('Vivanta Kathmandu', 'Kathmandu', 'Vivanta Kathmandu lets youstep into the best hotel rooms in Kathmandu,where attentive service meets unparalleled flexibility', '$100/night', 4.8, 'https://pix8.agoda.net/hotelImages/64197218/0/e4c556271ec41587a3fbccbed3702643.jpg?ce=0&s=1024x'),
('City Lights Hotel', 'Kathmandu', 'Stay in the heart of the city and enjoy easy access to top attractions.', '$175/night', 4.2, 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=600&q=80'),
('Kathmandu Marriott Hotel', 'Kathmandu', 'Enjoy Your Stay In Kathmandu — Get the Best Rates For Your Stay At Kathmandu Marriott Hotel.', '$120/night', 4.7, 'https://media.istockphoto.com/id/1364623517/photo/woman-swims-in-tropical-sea-split-screen-underwater-shot.webp?a=1&b=1&s=612x612&w=0&k=20&c=jO75BDD1JK9CNm4Mkc1zi9dZ827CVRcxBQN680kzD64='),
('Harbor View Hotel', 'Pokhara', 'Overlooking the harbor, this hotel offers stunning water views and fresh seafood dining.', '$230/night', 4.6, 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'),
('Desert Oasis Resort', 'Mustang', 'A unique desert experience with luxury pools and spa services in a tranquil setting.', '$185/night', 4.5, 'https://images.unsplash.com/photo-1511746315387-c4a76980c9a2?auto=format&fit=crop&w=600&q=80'); 