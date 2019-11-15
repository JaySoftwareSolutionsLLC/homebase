SELECT  d.id
		, d.name
		, d.description
        , d.golden_mean
        , ( SELECT rating
            FROM personal_wellness_dimension_logs AS dl
            WHERE d.id = dl.dimension_id
            ORDER BY dl.datetime DESC
            LIMIT 1 ) AS 'mr_rating'

FROM personal_wellness_dimensions AS d