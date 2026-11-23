-- TaskFlow Seed Data
USE `taskflow_db`;

-- Default Password for all seed users: Password123!
-- Generated with password_hash('Password123!', PASSWORD_BCRYPT)
-- Hash: $2y$10$wN3tVqH7kM9oK5u1lY8ZFeR9zM4n3b2v1x9y8z7w6v5u4t3s2r1qP

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(1, 'Alex Morgan', 'admin@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'admin', 'active'),
(2, 'Sarah Jenkins', 'leader@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'team_leader', 'active'),
(3, 'David Chen', 'lead2@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'team_leader', 'active'),
(4, 'Emma Watson', 'member@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'team_member', 'active'),
(5, 'Liam Patel', 'liam@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'team_member', 'active'),
(6, 'Olivia Taylor', 'olivia@taskflow.local', '$2y$10$/sO5FM7ikBD0rJGnv3/C9.TPSkLNjMr032NoUS3dycT0c4SIKNV1e', 'team_member', 'active');

-- Teams
INSERT INTO `teams` (`id`, `name`, `description`, `leader_id`) VALUES
(1, 'Core Product Engineering', 'Cross-functional engineering team building core web and API features.', 2),
(2, 'UX & Design Systems', 'Design team maintaining accessibility, design systems, and frontend ergonomics.', 3);

-- Team Members
INSERT INTO `team_members` (`team_id`, `user_id`) VALUES
(1, 2),
(1, 4),
(1, 5),
(2, 3),
(2, 6);

-- Projects
INSERT INTO `projects` (`id`, `name`, `description`, `team_id`, `manager_id`, `status`, `start_date`, `deadline`) VALUES
(1, 'TaskFlow SaaS V1 Launch', 'Complete release of core task tracking, analytics, and team collaboration features.', 1, 2, 'active', '2026-11-01', '2026-12-15'),
(2, 'Enterprise Design System Redesign', 'Harmonize UI components, tokenized colors, and responsive layouts across platforms.', 2, 3, 'active', '2026-11-10', '2026-12-30'),
(3, 'API Integration Pipeline', 'RESTful external webhook connector and data export architecture.', 1, 2, 'planning', '2026-12-01', '2027-01-20');

-- Tasks
INSERT INTO `tasks` (`id`, `title`, `description`, `project_id`, `team_id`, `assigned_to`, `created_by`, `priority`, `status`, `start_date`, `due_date`) VALUES
(1, 'Implement Role-Based Access Control', 'Enforce backend record-level authorization across Admin, Leader, and Member roles.', 1, 1, 4, 2, 'urgent', 'in_progress', '2026-11-20', '2026-11-28'),
(2, 'CSRF & XSS Security Audit', 'Ensure all forms embed valid CSRF tokens and output is escaped using htmlspecialchars.', 1, 1, 5, 2, 'high', 'completed', '2026-11-15', '2026-11-22'),
(3, 'Interactive Kanban Board', 'Create drag-and-drop or click-to-move board for task states with AJAX sync.', 1, 1, 4, 2, 'high', 'to_do', '2026-11-22', '2026-12-05'),
(4, 'Build Color Tokens & Dark UI Mode', 'Construct CSS custom properties for sleek SaaS aesthetics and high contrast.', 2, 2, 6, 3, 'medium', 'review', '2026-11-18', '2026-11-29'),
(5, 'Database Query Optimization', 'Add foreign key indices and examine query execution plans for dashboard metrics.', 1, 1, 5, 2, 'medium', 'in_progress', '2026-11-21', '2026-12-02'),
(6, 'Mobile View Navigation Bar', 'Implement responsive slide-out sidebar for mobile and tablet touchscreens.', 2, 2, 6, 3, 'low', 'blocked', '2026-11-19', '2026-11-30');

-- Task Comments
INSERT INTO `task_comments` (`task_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 2, 'Please make sure team leaders cannot edit members outside their assigned team.', '2026-11-21 10:15:00'),
(1, 4, 'Working on this right now! Models and middleware have been updated with strict ownership checks.', '2026-11-22 14:30:00'),
(2, 5, 'Audit complete: all POST routes now verify CSRF tokens and responses sanitize parameters.', '2026-11-22 16:45:00'),
(4, 3, 'Color contrast passes WCAG AA standards. Ready for leader review.', '2026-11-23 09:00:00');

-- Notifications
INSERT INTO `notifications` (`user_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(4, 'New Task Assigned', 'Sarah Jenkins assigned you to: Implement Role-Based Access Control', '/tasks/view?id=1', 0, '2026-11-20 11:00:00'),
(4, 'Comment on Task #1', 'Sarah Jenkins commented on your assigned task.', '/tasks/view?id=1', 0, '2026-11-21 10:15:00'),
(5, 'Task Completed', 'CSRF & XSS Security Audit has been marked as Completed.', '/tasks/view?id=2', 1, '2026-11-22 16:45:00');

-- Activity Logs
INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `description`, `created_at`) VALUES
(1, 'create', 'team', 1, 'Admin created team "Core Product Engineering"', '2026-11-01 09:00:00'),
(1, 'create', 'team', 2, 'Admin created team "UX & Design Systems"', '2026-11-01 09:15:00'),
(2, 'create', 'project', 1, 'Sarah Jenkins created project "TaskFlow SaaS V1 Launch"', '2026-11-01 10:00:00'),
(2, 'create', 'task', 1, 'Sarah Jenkins created task "Implement Role-Based Access Control"', '2026-11-20 11:00:00'),
(5, 'update_status', 'task', 2, 'Liam Patel marked task #2 as completed', '2026-11-22 16:45:00');
