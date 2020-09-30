INSERT INTO `hootus`.`zone`
(`zone`)
VALUES ('Zone-1'),('Zone-2'),('Zone-3'),('Zone-4'),('Zone-5'),('Zone-6'),('Zone-7'),('Zone-8'),('Zone-9'),('Zone-10');

SELECT * FROM zone;


INSERT INTO `hootus`.`location`
(`zone_id`,`locationID`, `locationName`, `residentName`, `primaryContact`, `alternateContact`, `notes`, `status`, `lastAlarm`, `lastAlarmTime`)
VALUES
(1,4001,'Floor 1','Fiona Lewis','9900105081','8800105081','notes 4001',1,0,'2018-07-08 11:27:36'),
(1,4002,'Floor 2','Steven Short','9900105082','8800105082','notes 4002',1,0,'2018-07-08 11:17:24'),
(1,4003,'Floor 3','Jessica Mathis','9900105083','8800105083','notes 4003',1,0,'2018-07-08 11:05:13'),
(1,4004,'Floor 4','Jennifer Duncan','9900105084','8800105084','notes 4004',1,0,'2018-07-08 11:01:46'),
(1,4005,'Floor 5','Chloe North','9900105085','8800105085','notes 4005',1,0,'2018-07-08 11:16:12'),
(1,4006,'Floor 6','Benjamin Hemmings','9900105086','8800105086','notes 4006',1,0,'2018-07-08 11:46:38'),
(1,4007,'Floor 7','Robert Smith','9900105087','8800105087','notes 4007',1,0,'2018-07-08 11:14:54'),
(1,4008,'Floor 8','Theresa Ince','9900105088','8800105088','notes 4008',1,0,'2018-07-08 11:31:06'),
(1,4009,'Floor 9','Wanda Tucker','9900105089','8800105089','notes 4009',1,1,'2018-07-08 11:16:50'),
(1,4010,'Floor 10','Stephen Russell','9900105090','8800105090','notes 4010',1,0,'2018-07-08 11:30:02'),
(2,4011,'Floor 11','Michael White','9900105091','8800105091','notes 4011',1,1,'2018-07-08 11:33:50'),
(2,4012,'Floor 12','Alan Abraham','9900105092','8800105092','notes 4012',1,0,'2018-07-08 11:41:39'),
(2,4013,'Floor 13','Colin Morrison','9900105093','8800105093','notes 4013',1,1,'2018-07-08 11:39:19'),
(2,4014,'Floor 14','Dorothy Young','9900105094','8800105094','notes 4014',1,0,'2018-07-08 11:48:48'),
(2,4015,'Floor 15','Sarah Paterson','9900105095','8800105095','notes 4015',1,1,'2018-07-08 11:54:04'),
(2,4016,'Floor 16','Ella Dowd','9900105096','8800105096','notes 4016',1,0,'2018-07-08 11:14:59'),
(2,4017,'Floor 17','Peter Kerr','9900105097','8800105097','notes 4017',1,1,'2018-07-08 11:35:47'),
(2,4018,'Floor 18','Jacob Bailey','9900105098','8800105098','notes 4018',1,0,'2018-07-08 11:47:26'),
(2,4019,'Floor 19','David James','9900105099','8800105099','notes 4019',1,0,'2018-07-08 11:47:42'),
(2,4020,'Floor 20','Victor Ferguson','9900105100','8800105100','notes 4020',1,1,'2018-07-08 11:36:12'),
(3,4021,'Floor 21','Caroline Poole','9900105101','8800105101','notes 4021',1,1,'2018-07-08 11:15:47'),
(3,4022,'Floor 22','Stephanie Thomson','9900105102','8800105102','notes 4022',1,0,'2018-07-08 11:32:27'),
(3,4023,'Floor 23','Karen Payne','9900105103','8800105103','notes 4023',1,1,'2018-07-08 11:58:02'),
(3,4024,'Floor 24','Jessica Morrison','9900105104','8800105104','notes 4024',1,0,'2018-07-08 11:19:49'),
(3,4025,'Floor 25','Victor Davies','9900105105','8800105105','notes 4025',1,0,'2018-07-08 11:23:05'),
(3,4026,'Floor 26','Raj  Kumar','9900105106','8800105106','notes 4026',1,0,'2018-07-08 11:09:32'),
(3,4027,'Floor 27','Ramki S','9900105107','8800105107','notes 4027',1,0,'2018-07-08 11:23:16'),
(3,4028,'Floor 28','Tejas  R','9900105108','8800105108','notes 4028',1,0,'2018-07-08 11:52:56'),
(3,4029,'Floor 29','Suni R','9900105109','8800105109','notes 4029',1,1,'2018-07-08 11:53:22'),
(3,4030,'Floor 30','Rama PV','9900105110','8800105110','notes 4030',1,1,'2018-07-08 11:20:49');

INSERT INTO `hootus`.`todo`
(`task`,`createdBy`, `owner`, `dueDate`, `status`, `comments`)
VALUES
('Test Task 1',2732,2792,'2018-07-10 12:58:22','Open','Task 1 Comments'),
('Test Task 2',2856,2722,'2018-07-09 23:37:42','Open','Task 2 Comments'),
('Test Task 3',2681,2794,'2018-07-14 15:05:47','Open','Task 3 Comments'),
('Test Task 4',2724,2672,'2018-07-14 06:00:44','Open','Task 4 Comments'),
('Test Task 5',2707,2695,'2018-07-22 23:54:51','Open','Task 5 Comments'),
('Test Task 6',2797,2718,'2018-07-29 15:55:25','Open','Task 6 Comments'),
('Test Task 7',2684,2710,'2018-07-12 18:40:06','Open','Task 7 Comments'),
('Test Task 8',2867,2878,'2018-07-18 00:17:22','Open','Task 8 Comments'),
('Test Task 9',2722,2720,'2018-07-20 20:02:40','Open','Task 9 Comments'),
('Test Task 10',2833,2695,'2018-07-11 08:09:37','Open','Task 10 Comments'),
('Test Task 11',2850,2742,'2018-07-27 04:39:04','Open','Task 11 Comments'),
('Test Task 12',2749,2769,'2018-07-09 21:15:36','Open','Task 12 Comments'),
('Test Task 13',2764,2879,'2018-07-26 10:36:56','Open','Task 13 Comments'),
('Test Task 14',2769,2775,'2018-07-14 04:10:28','Open','Task 14 Comments'),
('Test Task 15',2793,2838,'2018-07-11 23:14:48','Open','Task 15 Comments'),
('Test Task 16',2888,2717,'2018-07-22 22:33:06','Open','Task 16 Comments'),
('Test Task 17',2822,2800,'2018-07-29 09:40:02','Open','Task 17 Comments'),
('Test Task 18',2707,2686,'2018-07-25 21:21:31','Open','Task 18 Comments'),
('Test Task 19',2845,2836,'2018-07-08 13:22:24','Open','Task 19 Comments'),
('Test Task 20',2810,2677,'2018-07-20 13:56:39','Open','Task 20 Comments'),
('Test Task 21',2791,2769,'2018-07-11 16:24:00','Open','Task 21 Comments'),
('Test Task 22',2815,2788,'2018-07-15 22:50:50','OnHold','Task 22 Comments'),
('Test Task 23',2726,2848,'2018-07-29 11:20:42','OnHold','Task 23 Comments'),
('Test Task 24',2698,2805,'2018-07-10 06:38:36','OnHold','Task 24 Comments'),
('Test Task 25',2863,2759,'2018-07-20 10:17:14','OnHold','Task 25 Comments'),
('Test Task 26',2845,2766,'2018-07-22 01:50:43','OnHold','Task 26 Comments'),
('Test Task 27',2884,2805,'2018-07-29 05:59:24','OnHold','Task 27 Comments'),
('Test Task 28',2680,2859,'2018-07-15 04:43:59','Open','Task 28 Comments'),
('Test Task 29',2778,2842,'2018-07-26 23:13:46','Open','Task 29 Comments'),
('Test Task 30',2845,2728,'2018-07-28 09:14:33','Open','Task 30 Comments'),
('Test Task 31',2693,2879,'2018-07-19 10:31:44','Open','Task 31 Comments'),
('Test Task 32',2852,2786,'2018-07-08 09:20:27','Open','Task 32 Comments'),
('Test Task 33',2698,2686,'2018-07-12 14:01:33','Open','Task 33 Comments'),
('Test Task 34',2706,2879,'2018-07-10 00:13:02','Open','Task 34 Comments'),
('Test Task 35',2883,2885,'2018-07-22 19:14:26','Open','Task 35 Comments'),
('Test Task 36',2685,2685,'2018-07-09 12:07:34','Open','Task 36 Comments'),
('Test Task 37',2826,2681,'2018-07-09 05:23:07','Open','Task 37 Comments'),
('Test Task 38',2778,2868,'2018-07-21 19:47:49','Open','Task 38 Comments'),
('Test Task 39',2765,2705,'2018-07-22 19:11:44','Open','Task 39 Comments'),
('Test Task 40',2786,2766,'2018-07-11 12:49:40','Open','Task 40 Comments');