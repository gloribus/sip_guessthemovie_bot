-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 25 2019 г., 02:51
-- Версия сервера: 10.1.26-MariaDB-0+deb9u1
-- Версия PHP: 7.0.33-0+deb9u5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `rg.guessthemovie`
--

-- --------------------------------------------------------

--
-- Структура таблицы `movie_list`
--

CREATE TABLE `movie_list` (
  `id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `film_title` varchar(64) NOT NULL,
  `film_overview` text NOT NULL,
  `film_backdrop` varchar(128) NOT NULL,
  `film_poster` varchar(64) NOT NULL,
  `answer_1` varchar(64) NOT NULL,
  `answer_2` varchar(64) NOT NULL,
  `answer_3` varchar(64) NOT NULL,
  `answer_4` varchar(64) NOT NULL,
  `answer_5` varchar(64) NOT NULL,
  `answer_6` varchar(64) NOT NULL,
  `film_easy_start` tinyint(1) NOT NULL,
  `film_hard_end` tinyint(1) NOT NULL,
  `film_mark` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `vk_id` int(11) NOT NULL,
  `user_name` varchar(128) NOT NULL,
  `user_surname` varchar(128) NOT NULL,
  `points_record` int(11) NOT NULL,
  `streak_record` int(11) NOT NULL,
  `all_win` int(11) NOT NULL,
  `all_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users_rating`
--

CREATE TABLE `users_rating` (
  `id` int(11) NOT NULL,
  `vk_id` int(11) NOT NULL,
  `points_week` int(11) NOT NULL,
  `time_week` int(11) NOT NULL,
  `points_month` int(11) NOT NULL,
  `time_month` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `vk_id` int(11) NOT NULL,
  `films` text NOT NULL,
  `streak` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT '1',
  `session_start` int(11) NOT NULL,
  `session_end` int(11) NOT NULL,
  `session_time` int(11) NOT NULL,
  `service_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `movie_list`
--
ALTER TABLE `movie_list`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users_rating`
--
ALTER TABLE `users_rating`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `movie_list`
--
ALTER TABLE `movie_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1079;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17445;
--
-- AUTO_INCREMENT для таблицы `users_rating`
--
ALTER TABLE `users_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17606;
--
-- AUTO_INCREMENT для таблицы `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98005;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
