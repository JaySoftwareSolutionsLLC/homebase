SELECT *
FROM personal_wellness_habits AS h
INNER JOIN personal_wellness_habit_metric AS hm
    ON (h.id = hm.habit_id)
