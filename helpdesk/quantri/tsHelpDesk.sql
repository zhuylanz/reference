-- http://www.phpmyadmin.net
--
-- Generation Time: Aug 18, 2015 at 01:43 PM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ticketsys`
--

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_agent_level`
--

INSERT INTO `ticket_ts_agent_level` (`id`, `status`, `date_added`, `date_updated`) VALUES
(1, 1, '2015-07-01 14:49:22', '2015-07-01 14:49:22'),
(2, 1, '2015-07-01 14:49:49', '2015-07-01 14:49:49'),
(3, 1, '2015-07-01 14:49:55', '2015-07-01 14:49:55'),
(4, 1, '2015-07-01 14:50:05', '2015-07-01 14:50:05');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_agent_level_description`
--

INSERT INTO `ticket_ts_agent_level_description` (`id`, `level_id`, `name`, `description`, `language_id`) VALUES
(1, 1, 'Low', 'Agent has new but good in skills. ', 1),
(2, 2, 'High', 'Agent has High level of knowledge.', 1),
(3, 3, 'Pro', 'Agent has Pro and can handle all issues.', 1),
(4, 4, 'Expert', 'Agent has Expert level of knowledge.', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_agent_roles`
--

INSERT INTO `ticket_ts_agent_roles` (`id`, `agent_id`, `role_id`) VALUES
(1, 4, 8);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_business_hours`
--

INSERT INTO `ticket_ts_business_hours` (`id`, `name`, `description`, `timezone`, `date_added`, `date_updated`, `timings`, `sizes`, `positions`) VALUES
(1, 'India', 'This is like Indian working hours', 'Asia/Kolkata', '2015-06-03 19:29:50', '2015-08-18 13:26:24', 'a:4:{s:6:"monday";a:1:{i:0;s:13:"08:00 - 18:30";}s:7:"tuesday";a:2:{i:0;s:13:"05:00 - 11:00";i:1;s:13:"12:00 - 19:30";}s:8:"thursday";a:1:{i:0;s:13:"08:00 - 15:30";}s:6:"friday";a:1:{i:0;s:13:"07:30 - 16:30";}}', 'a:4:{s:6:"monday";a:1:{i:0;s:3:"529";}s:7:"tuesday";a:2:{i:0;s:3:"302";i:1;s:3:"381";}s:8:"thursday";a:1:{i:0;s:3:"375";}s:6:"friday";a:1:{i:0;s:3:"452";}}', 'a:4:{s:6:"monday";a:1:{i:0;s:3:"400";}s:7:"tuesday";a:2:{i:0;s:3:"250";i:1;s:3:"600";}s:8:"thursday";a:1:{i:0;s:3:"400";}s:6:"friday";a:1:{i:0;s:3:"375";}}'),
(2, 'Default', 'Default', 'Europe/London', '2015-06-03 19:36:27', '2015-08-18 13:25:15', 'a:4:{s:6:"monday";a:1:{i:0;s:13:"08:00 - 18:00";}s:7:"tuesday";a:1:{i:0;s:13:"06:00 - 14:00";}s:9:"wednesday";a:1:{i:0;s:13:"07:30 - 17:30";}s:6:"friday";a:1:{i:0;s:13:"06:30 - 14:30";}}', 'a:4:{s:6:"monday";a:1:{i:0;s:3:"504";}s:7:"tuesday";a:1:{i:0;s:3:"402";}s:9:"wednesday";a:1:{i:0;s:3:"502";}s:6:"friday";a:1:{i:0;s:3:"402";}}', 'a:4:{s:6:"monday";a:1:{i:0;s:3:"400";}s:7:"tuesday";a:1:{i:0;s:3:"300";}s:9:"wednesday";a:1:{i:0;s:3:"375";}s:6:"friday";a:1:{i:0;s:3:"325";}}');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_category`
--

INSERT INTO `ticket_ts_category` (`id`, `status`, `date_added`, `date_updated`) VALUES
(1, 1, '2015-07-07 17:40:13', '2015-07-07 17:40:13'),
(2, 1, '2015-07-07 19:43:39', '2015-07-07 19:43:39'),
(3, 1, '2015-07-07 19:44:17', '2015-07-07 19:44:17');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_category_description`
--

INSERT INTO `ticket_ts_category_description` (`id`, `category_id`, `name`, `description`, `language_id`) VALUES
(1, 1, 'Support Related Query', 'Oh you want Support .\r\nIs it related to Installation or any small issues ?\r\n\r\nWhy don''t you try our added docs FIRST :)', 1),
(2, 3, 'Error / Bug', 'Module Showing any issue on your site, can be Version issue. Please check given solution.\r\nMay be your issue will solve here.\r\n\r\nNo need to generate Ticket :)', 1),
(3, 2, 'Solved / Queries', 'In this category we Display customers and agents conversation that mostly occur.\r\nSo Public this for all, any issue with same problem will solve like this.\r\n\r\nNo need to generate tickets :)', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_category_information`
--

INSERT INTO `ticket_ts_category_information` (`id`, `category_id`, `information_id`) VALUES
(1, 2, 10),
(2, 1, 7),
(3, 1, 6),
(4, 2, 9),
(5, 2, 8),
(6, 3, 3),
(7, 3, 12),
(8, 3, 11);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_emailtemplates`
--

INSERT INTO `ticket_ts_emailtemplates` (`id`, `name`, `message`, `status`, `date_added`, `date_updated`) VALUES
(1, 'Ticket Created', '&lt;p&gt;Hi,&lt;/p&gt;&lt;p&gt;&amp;nbsp;{{ticket.description}}&lt;/p&gt;&lt;p&gt;Thanks &amp;nbsp;for generating ticket, we will solve your query as soon as possible.&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Regards,&lt;/p&gt;&lt;p&gt;Webkul Team&lt;/p&gt;', 1, '2015-08-04 18:46:51', '2015-08-04 19:55:32'),
(2, ' {{ticket.customername}} added query {{ticket.id}}', '&lt;p&gt;&amp;nbsp;&amp;nbsp;{{ticket.threaddescription}}&lt;/p&gt;', 1, '2015-08-10 11:31:09', '2015-08-18 13:37:07');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_events`
--

INSERT INTO `ticket_ts_events` (`id`, `name`, `description`, `performer`, `events`, `actions`, `conditions_all`, `conditions_one`, `status`, `date_added`, `date_updated`) VALUES
(1, 'Ticket Created ', 'Ticket Created ', 'a:1:{s:5:"value";s:8:"customer";}', 'a:1:{i:0;a:2:{s:4:"type";s:6:"ticket";s:4:"from";s:7:"created";}}', 'a:1:{i:0;a:2:{s:4:"type";s:13:"mail_customer";s:6:"action";a:3:{s:13:"emailtemplate";s:0:"";s:7:"subject";s:47:"We received your query and will reach you soon ";s:7:"message";s:524:"&lt;p&gt;Hi {{ticket.customername}},&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;line-height: 1.42857143;&quot;&gt;We received your query and will reach you soon .&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;line-height: 1.42857143;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;line-height: 1.42857143;&quot;&gt;Regards,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;line-height: 1.42857143;&quot;&gt;Webkul HelpDesk&lt;br&gt;&lt;/span&gt;&lt;br&gt;&lt;/p&gt;";}}}', 'a:1:{i:1;a:3:{s:4:"type";s:6:"source";s:14:"matchCondition";s:2:"is";s:5:"match";s:3:"web";}}', 'a:1:{i:0;a:3:{s:4:"type";s:6:"source";s:14:"matchCondition";s:2:"is";s:5:"match";s:4:"mail";}}', 1, '2015-07-30 13:17:26', '2015-08-18 13:34:47'),
(2, 'Ticket Update', 'Ticket Update', 'a:2:{s:5:"value";s:6:"agents";s:6:"agents";a:1:{i:0;s:3:"all";}}', 'a:3:{i:0;a:3:{s:4:"type";s:16:"priority_updated";s:4:"from";s:3:"any";s:2:"to";s:3:"any";}i:1;a:3:{s:4:"type";s:13:"agent_updated";s:4:"from";s:3:"any";s:2:"to";s:3:"any";}i:3;a:3:{s:4:"type";s:13:"group_updated";s:4:"from";s:3:"any";s:2:"to";s:3:"any";}}', 'a:1:{i:0;a:2:{s:4:"type";s:13:"mail_customer";s:6:"action";a:3:{s:13:"emailtemplate";s:0:"";s:7:"subject";s:33:"Your Ticket{{ticket.id}} updated ";s:7:"message";s:180:"&lt;p&gt;Hi,&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Your Ticket updated.&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Regards,&lt;/p&gt;&lt;p&gt;Webkul Helpdesk&lt;/p&gt;";}}}', 'a:0:{}', 'a:1:{i:1;a:3:{s:4:"type";s:7:"subject";s:14:"matchCondition";s:8:"contains";s:5:"match";s:5:"Sales";}}', 1, '2015-07-06 20:50:21', '2015-08-18 13:36:23'),
(3, 'Send Mail to Customer after adding reply to ticket', 'Send Mail to Customer after adding reply to ticket', 'a:2:{s:5:"value";s:6:"agents";s:6:"agents";a:1:{i:0;s:3:"all";}}', 'a:1:{i:0;a:1:{s:4:"type";s:11:"reply_added";}}', 'a:1:{i:0;a:2:{s:4:"type";s:13:"mail_customer";s:6:"action";a:3:{s:13:"emailtemplate";s:0:"";s:7:"subject";s:54:" {{ticket.agentname}} replied you ticket {{ticket.id}}";s:7:"message";s:57:"&lt;p&gt;&amp;nbsp;{{ticket.threaddescription}}&lt;/p&gt;";}}}', 'a:0:{}', 'a:1:{i:0;a:3:{s:4:"type";s:7:"created";s:14:"matchCondition";s:5:"after";s:5:"match";s:10:"2015-08-01";}}', 1, '2015-08-05 19:02:12', '2015-08-18 13:33:09'),
(4, 'Send Mail to Agent after adding query to ticket', 'Send Mail to Agent after adding query to ticket', 'a:1:{s:5:"value";s:8:"customer";}', 'a:1:{i:0;a:1:{s:4:"type";s:11:"reply_added";}}', 'a:1:{i:2;a:2:{s:4:"type";s:10:"mail_agent";s:6:"action";a:4:{s:5:"agent";s:7:"current";s:13:"emailtemplate";s:1:"2";s:7:"subject";s:0:"";s:7:"message";s:29:"&lt;p&gt;&lt;br&gt;&lt;/p&gt;";}}}', 'a:1:{i:0;a:3:{s:4:"type";s:6:"source";s:14:"matchCondition";s:2:"is";s:5:"match";s:4:"mail";}}', 'a:1:{i:0;a:3:{s:4:"type";s:6:"source";s:14:"matchCondition";s:2:"is";s:5:"match";s:3:"web";}}', 1, '2015-08-10 11:31:28', '2015-08-18 13:32:43');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_groups`
--

INSERT INTO `ticket_ts_groups` (`id`, `automatic_assign`, `inform_time`, `inform_agent`, `date_added`, `date_updated`, `businesshour_id`) VALUES
(1, 1, '30', 2, '2015-06-08 15:00:59', '2015-08-18 13:20:50', 1),
(2, 1, '120', 2, '2015-06-08 15:22:46', '2015-08-18 13:20:58', 2);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_group_descriptions`
--

INSERT INTO `ticket_ts_group_descriptions` (`id`, `group_id`, `name`, `description`, `language_id`) VALUES
(1, 1, 'Symfony', 'Symfony', 1),
(2, 2, 'Opencart', 'Opencart support extension module is a next generation support ticket management system which allows tons of modern ticket system features including email piping.', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_holidays`
--

INSERT INTO `ticket_ts_holidays` (`id`, `business_hour_id`, `name`, `from_date`, `to_date`, `date_added`, `date_updated`) VALUES
(1, 2, 'New Year Party', '2016-01-03', '2015-12-30', '2015-08-18 13:25:15', '2015-08-18 13:25:15'),
(2, 2, 'christmas', '2015-12-25', '2015-12-25', '2015-08-18 13:25:15', '2015-08-18 13:25:15');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_organizations`
--

INSERT INTO `ticket_ts_organizations` (`id`, `name`, `description`, `domain`, `note`, `image`, `customer_role`, `date_added`, `date_updated`) VALUES
(1, 'Webkul', 'Webkul', 'webkul.com', 'Webkul', '5', 1, '2015-07-16 15:05:22', '2015-07-16 15:22:52');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_responses`
--

INSERT INTO `ticket_ts_responses` (`id`, `name`, `actions`, `valid_for`, `date_added`, `date_updated`, `status`, `description`) VALUES
(1, 'Apply to Opencart', 'a:4:{i:0;a:2:{s:4:"type";s:12:"assign_group";s:6:"action";s:1:"3";}i:1;a:2:{s:4:"type";s:8:"priority";s:6:"action";s:1:"1";}i:2;a:2:{s:4:"type";s:4:"note";s:6:"action";a:1:{s:4:"note";s:104:"Hi  {{ticket.customeremail}},\r\n\r\nPlease provide your FTP and Opencart Admin details.\r\n\r\nRegards,\r\nNikhil";}}i:3;a:2:{s:4:"type";s:10:"mail_agent";s:6:"action";a:4:{s:5:"agent";s:6:"assign";s:13:"emailtemplate";s:1:"1";s:7:"subject";s:0:"";s:7:"message";s:29:"&lt;p&gt;&lt;br&gt;&lt;/p&gt;";}}}', 'a:2:{s:5:"value";s:6:"groups";s:6:"groups";a:1:{i:0;s:1:"3";}}', '2015-07-27 17:59:05', '2015-08-18 13:27:45', 1, 'Using this Response, you can assign ticket to Opencart and asked Customer for FTP details.');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_roles`
--

INSERT INTO `ticket_ts_roles` (`id`, `name`, `description`, `role`, `date_added`, `date_updated`) VALUES
(1, 'Admin', 'Admin can access complete HelpDesk system and can manage all pages and functionality.', 'a:3:{s:7:"tickets";a:13:{i:0;s:7:"tickets";i:1;s:14:"tickets.create";i:2;s:14:"tickets.delete";i:3;s:14:"tickets.assign";i:4;s:14:"tickets.update";i:5;s:12:"tickets.edit";i:6;s:13:"tickets.merge";i:7;s:13:"tickets.split";i:8;s:13:"tickets.addcc";i:9;s:13:"tickets.reply";i:10;s:15:"tickets.forword";i:11;s:16:"tickets.internal";i:12;s:20:"tickets.deletethread";}s:6:"agents";a:4:{i:0;s:6:"agents";i:1;s:10:"agents.add";i:2;s:11:"agents.edit";i:3;s:13:"agents.delete";}s:5:"admin";a:22:{i:0;s:5:"admin";i:1;s:14:"admin.activity";i:2;s:11:"admin.types";i:3;s:14:"admin.priority";i:4;s:12:"admin.status";i:5;s:18:"admin.customfields";i:6;s:19:"admin.ticketsfields";i:7;s:10:"admin.tags";i:8;s:12:"admin.emails";i:9;s:20:"admin.emailtemplates";i:10;s:11:"admin.level";i:11;s:11:"admin.roles";i:12;s:15:"admin.responses";i:13;s:12:"admin.groups";i:14;s:15:"admin.customers";i:15;s:19:"admin.organizations";i:16;s:19:"admin.businesshours";i:17;s:9:"admin.sla";i:18;s:11:"admin.rules";i:19;s:12:"admin.events";i:20;s:19:"admin.supportcenter";i:21;s:15:"admin.reporting";}}', '2015-06-11 21:08:02', '2015-08-18 13:22:18'),
(2, 'Default', 'Default can access only assign HelpDesk system and can manage Tickets, reply those, edit those, can forward etc.\r\nFeel free to change :)', 'a:1:{s:7:"tickets";a:6:{i:0;s:7:"tickets";i:1;s:12:"tickets.edit";i:2;s:13:"tickets.addcc";i:3;s:13:"tickets.reply";i:4;s:15:"tickets.forword";i:5;s:16:"tickets.internal";}}', '2015-06-11 21:08:02', '2015-08-18 13:23:20');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_rules`
--

INSERT INTO `ticket_ts_rules` (`id`, `name`, `description`, `actions`, `conditions_one`, `conditions_all`, `status`, `date_added`, `date_updated`) VALUES
(1, 'Check if it''s for opencart', 'Check if it''s for opencart', 'a:1:{i:0;a:2:{s:4:"type";s:12:"assign_agent";s:6:"action";s:1:"0";}}', 'a:1:{i:0;a:3:{s:4:"type";s:22:"subject_or_description";s:14:"matchCondition";s:8:"contains";s:5:"match";s:8:"opencart";}}', 'a:1:{i:0;a:3:{s:4:"type";s:7:"subject";s:14:"matchCondition";s:8:"contains";s:5:"match";s:8:"opencart";}}', 1, '2015-07-29 18:59:03', '2015-07-29 18:59:03');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_sla`
--

INSERT INTO `ticket_ts_sla` (`id`, `name`, `description`, `status`, `sort_order`, `conditions_all`, `conditions_one`, `date_added`, `date_updated`) VALUES
(1, 'SLA Default', 'SLA Default', 1, 1, 'a:0:{}', 'a:0:{}', '2015-08-13 12:32:47', '2015-08-18 13:30:52');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_sla_priority`
--

INSERT INTO `ticket_ts_sla_priority` (`id`, `sla_id`, `priority_id`, `respond_within`, `resolve_within`, `hours_type`, `status`) VALUES
(1, 1, 1, 'a:2:{s:4:"time";s:2:"10";s:4:"type";s:6:"minute";}', 'a:2:{s:4:"time";s:1:"1";s:4:"type";s:4:"days";}', 1, 1),
(2, 1, 2, 'a:2:{s:4:"time";s:2:"20";s:4:"type";s:6:"minute";}', 'a:2:{s:4:"time";s:1:"5";s:4:"type";s:5:"hours";}', 0, 1),
(3, 1, 3, 'a:2:{s:4:"time";s:0:"";s:4:"type";s:3:"min";}', 'a:2:{s:4:"time";s:0:"";s:4:"type";s:6:"minute";}', 0, 0),
(4, 1, 4, 'a:2:{s:4:"time";s:0:"";s:4:"type";s:3:"min";}', 'a:2:{s:4:"time";s:0:"";s:4:"type";s:3:"min";}', 0, 0),
(5, 1, 5, 'a:2:{s:4:"time";s:0:"";s:4:"type";s:3:"min";}', 'a:2:{s:4:"time";s:0:"";s:4:"type";s:3:"min";}', 0, 0);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_priority`
--

INSERT INTO `ticket_ts_ticket_priority` (`id`, `status`, `date_added`, `date_updated`) VALUES
(1, 1, '2015-06-26 17:52:29', '2015-06-26 17:52:29'),
(2, 1, '2015-06-26 17:53:30', '2015-06-26 17:53:30'),
(3, 1, '2015-06-26 17:53:38', '2015-06-26 17:53:38'),
(4, 1, '2015-06-26 17:53:48', '2015-06-26 17:53:48'),
(5, 1, '2015-07-02 20:46:54', '2015-07-02 20:46:54');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_priority_description`
--

INSERT INTO `ticket_ts_ticket_priority_description` (`id`, `priority_id`, `name`, `description`, `language_id`) VALUES
(1, 1, 'High', 'If ticket is added with this priority means it''s related to big issue and agents will focus more on this priority added ticket(s)', 1),
(2, 2, 'Medium', '', 1),
(3, 3, 'Low', 'This Priority shows customer can wait and his/her task is not valuable than other customers.', 1),
(4, 4, 'Urgent', '', 1),
(5, 5, 'Webkul', 'This is Demo Priority.', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_status`
--

INSERT INTO `ticket_ts_ticket_status` (`id`, `status`, `date_added`, `date_updated`) VALUES
(3, 1, '2015-06-26 17:19:28', '2015-06-26 17:19:28'),
(4, 1, '2015-06-26 17:20:21', '2015-06-26 17:20:21'),
(5, 1, '2015-06-26 17:21:33', '2015-06-26 17:21:33'),
(6, 1, '2015-06-26 17:23:17', '2015-06-26 17:23:17'),
(7, 1, '2015-06-26 17:24:13', '2015-06-26 17:24:13'),
(8, 1, '2015-06-26 17:24:58', '2015-06-26 17:24:58'),
(9, 0, '2015-06-26 17:26:15', '2015-06-26 17:26:15');

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_status_description`
--

INSERT INTO `ticket_ts_ticket_status_description` (`id`, `status_id`, `name`, `description`, `language_id`) VALUES
(1, 3, 'Pending', 'Pending Status will be the status when ticket processing will be pending because of any reason.', 1),
(2, 4, 'New', 'When Ticket is generated by customer, New status will append to ticket. ', 1),
(3, 5, 'Open', 'Open status refer to a ticket that is not solved yet.', 1),
(4, 6, 'Closed', 'Closed status refer that agents are not working on this status applied ticket(s).', 1),
(5, 7, 'Resolved', 'For those tickets which once are successfully solved for both end and customer is satisfied :)', 1),
(6, 8, 'Spam', 'Status for those ticket which once are spam, not  real  ticket etc', 1),
(7, 9, 'Waiting For Customer', 'This status can be used is agents are working properly but form customer end they are not getting proper reply or getting very rarely.', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_types`
--

INSERT INTO `ticket_ts_ticket_types` (`id`, `date_added`, `date_updated`, `status`) VALUES
(8, '2015-06-09 13:02:29', '2015-06-09 13:02:29', 1),
(7, '2015-06-09 13:02:03', '2015-06-09 13:02:03', 1),
(6, '2015-06-08 20:16:41', '2015-06-08 20:16:41', 1),
(9, '2015-06-09 13:02:52', '2015-06-09 13:02:52', 1),
(10, '2015-07-06 19:47:29', '2015-07-06 19:47:29', 1),
(11, '2015-07-06 19:47:37', '2015-07-06 19:47:37', 1),
(12, '2015-07-06 19:48:16', '2015-07-06 19:48:16', 0);

-- --------------------------------------------------------

--
-- Dumping data for table `ticket_ts_ticket_types_description`
--

INSERT INTO `ticket_ts_ticket_types_description` (`id`, `type_id`, `name`, `description`, `language_id`) VALUES
(108, 7, 'Support', 'If customer is has any any problem regarding product / service and need support.', 1),
(105, 6, 'Pre-Sale Query', 'If customer is asking for any product and service in advance.', 1),
(107, 8, 'Refund', 'If customer is asking for refund :(', 1),
(106, 9, 'Question', 'If customer is has any question regarding any product and service.', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
