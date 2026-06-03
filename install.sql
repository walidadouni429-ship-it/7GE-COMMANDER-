CREATE DATABASE IF NOT EXISTS sge_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sge_erp;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin','commercial','technicien') DEFAULT 'commercial',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(150),
    adresse TEXT,
    gouvernorat VARCHAR(50),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(30) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    utilisateur_id INT,
    total_ht DECIMAL(12,3) DEFAULT 0,
    total_tva DECIMAL(12,3) DEFAULT 0,
    timbre DECIMAL(6,3) DEFAULT 1.000,
    total_ttc DECIMAL(12,3) DEFAULT 0,
    statut ENUM('pending','accepted','refused') DEFAULT 'pending',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE devis_lignes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    designation VARCHAR(200) NOT NULL,
    prix_unitaire_ht DECIMAL(12,3) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    total_ligne_ht DECIMAL(12,3) NOT NULL,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);

INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('Admin', 'admin@sevengenenergy.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');