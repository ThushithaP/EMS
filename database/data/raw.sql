INSERT INTO `perm_sections` (`id`, `section_name`, `section_status`) VALUES (NULL, 'Department', 'A');

INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'List Department', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'Add Department', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'Edit Department', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'View Department', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'Delete Department', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'Department Export To Excel', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '1', 'Department Status Change', 'A');

INSERT INTO `perm_sections` (`id`, `section_name`, `section_status`) VALUES (NULL, 'Staff', 'A');

INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'List Staff', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Add Staff', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Edit Staff', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'View Staff', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Delete Staff', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Staff Export To Excel', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Staff Status Change', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Change Staff Permission', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '2', 'Reset Staff Password', 'A');

INSERT INTO `perm_sections` (`id`, `section_name`, `section_status`) VALUES (NULL, 'Leave', 'A');

INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'List Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Add Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Edit Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'View Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Delete Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Leave Export To Excel', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Approve Leave', 'A');
INSERT INTO `perm_actions` (`id`, `section_id`, `action_name`, `section_status`) VALUES (NULL, '3', 'Unapprove Leave', 'A');

-- set all permission to user(ID=1)
INSERT INTO permissions (action_id, user_id)
SELECT pa.id, 1 
FROM perm_actions pa
LEFT JOIN permissions p ON pa.id = p.action_id AND p.user_id = 1
WHERE p.id IS NULL;
