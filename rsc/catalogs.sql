INSERT INTO roles (id, role) values (1, 'Administrador');
INSERT INTO roles (id, role) values (2, 'Vendedor');
INSERT INTO roles (id, role) values (3, 'Bodega');

INSERT INTO movements_type (id, `type`) VALUES (1,'PURCHASE');
INSERT INTO movements_type (id, `type`) VALUES (2,'SALE');
INSERT INTO movements_type (id, `type`) VALUES (3,'RETURN');

INSERT INTO purchases_status (id, status) VALUES (1,'EDITTING');
INSERT INTO purchases_status (id, status) VALUES (2,'APPLIED');

INSERT INTO sales_status (id, status) VALUES (1,'EDITTING');
INSERT INTO sales_status (id, status) VALUES (2,'APPLIED');
INSERT INTO sales_status (id, status) VALUES (3,'DISPATCHED');
INSERT INTO sales_status (id, status) VALUES (4,'DELIVERED');
INSERT INTO sales_status (id, status) VALUES (5,'RETURNED');
INSERT INTO sales_status (id, status) VALUES (6,'PARTIALLY_RETURNED');

INSERT INTO users (username, password, role_id) VALUES ('coagus', 'admin', 1);

CREATE VIEW v_stock AS
SELECT p.id, p.name, p.description, p.cost, p.price, 
	p.stock + 
		SUM(CASE 
				WHEN m.type_id IN (1,3) THEN m.quantity 
				WHEN m.type_id = 2 THEN m.quantity*-1 
                ELSE 0 
            END) AS stock,
	SUM(IFNULL(mp.quantity,0)) AS pending
FROM products p 
	LEFT JOIN movements m ON m.product_id = p.id AND m.apply = 1 
	LEFT JOIN movements mp ON mp.product_id = p.id AND mp.apply = 0 AND mp.type_id = 2
GROUP BY p.id, p.name, p.description, p.cost, p.price, p.stock;