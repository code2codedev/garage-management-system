USE gms_db;

INSERT INTO users (username, email, password_hash, role, status, created_at)
VALUES (
    'admin', 
    'admin@alphagarage.com', 
    '$2y$10$OUoRbWDQMtjSWzx1ZPJ7x.rUelc9/CEFm8XkMpKfL9NuI2GX6WsSK', 
    'admin', 
    'active', 
    NOW()
);