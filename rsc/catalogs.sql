INSERT INTO roles (id, role) values (1, 'Administrador');
INSERT INTO roles (id, role) values (2, 'Vendedor');
INSERT INTO roles (id, role) values (3, 'Bodega');

INSERT INTO purchases_status (id, status) VALUES (1,'EDITTNG');
INSERT INTO purchases_status (id, status) VALUES (2,'APPLIED');

INSERT INTO users (username, password, role_id) VALUES ('coagus', 'admin', 1);