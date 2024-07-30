#PHP Comic Indexer
The PHP Comic Indexer is a web application designed to manage and display comic strip panels. Here’s an overview of its features:

##Features

-**Comic Upload and Organization**: Users can upload comic strip panels, which are organized into folders by date (e.g., "20240710" or "20240730"). Each panel is uploaded as a separate image and shown in alphanumerical order based on filename ("Panel1", "Panel2", "Panel3").

-**Dynamic Display**: The frontend dynamically adjusts the display of comic panels based on the device resolution. On mobile devices, panels are shown one per line, while on larger screens, multiple panels may be displayed per line.

-**Low-Quality Images for Faster Loading**: For improved performance, the application creates a /lowquality folder within each comic folder. This folder contains resized images with reduced quality to ensure quick load times while maintaining high-quality versions for direct posting to platforms like Facebook or Instagram.

-**Social Media Sharing**: A SHARE bar is integrated into the website, allowing users to share their comics on Facebook, Twitter, Pinterest, and more. The application leverages OpenGraph tags for optimal representation on these platforms. 