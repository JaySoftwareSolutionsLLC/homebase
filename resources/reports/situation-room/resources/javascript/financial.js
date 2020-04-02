// $( document ).ready(function() {
    // "use strict";
/*---------------------------HISTORICAL DATA---------------------------------*/

    let sAndP = {
        '1950' : 31.71,
        '1951' : 24.02,
        '1952' : 18.37,
        '1953' : -0.99,
        '1954' : 52.65,
        '1955' : 31.56,
        '1956' : 6.56,
        '1957' : -10.78,
        '1958' : 43.36,
        '1959' : 11.96,
        '1960' : 0.47,
        '1961' : 26.89,
        '1962' : -8.73,
        '1963' : 22.8,
        '1964' : 16.48,
        '1965' : 12.45,
        '1966' : -10.06,
        '1967' : 23.98,
        '1968' : 11.06,
        '1969' : -8.5,
        '1970' : 4.01,
        '1971' : 14.31,
        '1972' : 18.98,
        '1973' : -14.66,
        '1974' : -26.47,
        '1975' : 37.2,
        '1976' : 23.84,
        '1977' : -7.18,
        '1978' : 6.56,
        '1979' : 18.44,
        '1980' : 32.5,
        '1981' : -4.92,
        '1982' : 21.55,
        '1983' : 22.56,
        '1984' : 6.27,
        '1985' : 31.73,
        '1986' : 18.76,
        '1987' : 5.25,
        '1988' : 16.61,
        '1989' : 31.69,
        '1990' : -3.11,
        '1991' : 30.47,
        '1992' : 7.62,
        '1993' : 10.08,
        '1994' : 1.32,
        '1995' : 37.58,
        '1996' : 22.96,
        '1997' : 33.36,
        '1998' : 28.58,
        '1999' : 21.04,
        '2000' : -9.11,
        '2001' : -11.89,
        '2002' : -22.1,
        '2003' : 28.68,
        '2004' : 10.88,
        '2005' : 4.91,
        '2006' : 15.79,
        '2007' : 5.49,
        '2008' : -37,
        '2009' : 26.46,
        '2010' : 15.06,
        '2011' : 2.11,
        '2012' : 16,
        '2013' : 32.39,
        '2014' : 13.69,
        '2015' : 1.38,
        '2016' : 11.96,
        '2017' : 21.83
        }
    let tenYearBond = {
        '1928' : 0.84,
        '1929' : 4.20,
        '1930' : 4.54,
        '1931' : -2.56,
        '1932' : 8.79,
        '1933' : 1.86,
        '1934' : 7.96,
        '1935' : 4.47,
        '1936' : 5.02,
        '1937' : 1.38,
        '1938' : 4.21,
        '1939' : 4.41,
        '1940' : 5.4,
        '1941' : -2.02,
        '1942' : 2.29,
        '1943' : 2.49,
        '1944' : 2.58,
        '1945' : 3.80,
        '1946' : 3.13,
        '1947' : 0.92,
        '1948' : 1.95,
        '1949' : 4.66,
        '1950' : 0.43,
        '1951' : -0.30,
        '1952' : 2.27,
        '1953' : 4.14,
        '1954' : 3.29,
        '1955' : -1.34,
        '1956' : -2.26,
        '1957' : 6.80,
        '1958' : -2.10,
        '1959' : -2.65,
        '1960' : 11.64,
        '1961' : 2.06,
        '1962' : 5.69,
        '1963' : 1.68,
        '1964' : 3.73,
        '1965' : 0.72,
        '1966' : 2.91,
        '1967' : -1.58,
        '1968' : 3.27,
        '1969' : -5.01,
        '1970' : 16.75,
        '1971' : 9.79,
        '1972' : 2.82,
        '1973' : 3.66,
        '1974' : 1.99,
        '1975' : 3.61,
        '1976' : 15.98,
        '1977' : 1.29,
        '1978' : -0.78,
        '1979' : 0.67,
        '1980' : -2.99,
        '1981' : 8.20,
        '1982' : 32.81,
        '1983' : 3.20,
        '1984' : 13.73,
        '1985' : 25.71,
        '1986' : 24.28,
        '1987' : -4.96,
        '1988' : 8.22,
        '1989' : 17.69,
        '1990' : 6.24,
        '1991' : 15.00,
        '1992' : 9.36,
        '1993' : 14.21,
        '1994' : -8.04,
        '1995' : 23.48,
        '1996' : 1.43,
        '1997' : 9.94,
        '1998' : 14.92,
        '1999' : -8.25,
        '2000' : 16.66,
        '2001' : 5.57,
        '2002' : 15.12,
        '2003' : 0.38,
        '2004' : 4.49,
        '2005' : 2.87,
        '2006' : 1.96,
        '2007' : 10.21,
        '2008' : 20.10,
        '2009' : -11.12,
        '2010' : 8.46,
        '2011' : 16.04,
        '2012' : 2.97,
        '2013' : -9.10,
        '2014' : 10.75,
        '2015' : 1.28,
        '2016' : 0.69,
        '2017' : 2.80,
    }

    let fourPercent = {
            '1914' : 4,
            '1915' : 4,
            '1916' : 4,
            '1917' : 4,
            '1918' : 4,
            '1919' : 4,
            '1920' : 4,
            '1921' : 4,
            '1922' : 4,
            '1923' : 4,
            '1924' : 4,
            '1925' : 4,
            '1926' : 4,
            '1927' : 4,
            '1928' : 4,
            '1929' : 4,
            '1930' : 4,
            '1931' : 4,
            '1932' : 4,
            '1933' : 4,
            '1934' : 4,
            '1935' : 4,
            '1936' : 4,
            '1937' : 4,
            '1938' : 4,
            '1939' : 4,
            '1940' : 4,
            '1941' : 4,
            '1942' : 4,
            '1943' : 4,
            '1944' : 4,
            '1945' : 4,
            '1946' : 4,
            '1947' : 4,
            '1948' : 4,
            '1949' : 4,
            '1950' : 4,
            '1951' : 4,
            '1952' : 4,
            '1953' : 4,
            '1954' : 4,
            '1955' : 4,
            '1956' : 4,
            '1957' : 4,
            '1958' : 4,
            '1959' : 4,
            '1960' : 4,
            '1961' : 4,
            '1962' : 4,
            '1963' : 4,
            '1964' : 4,
            '1965' : 4,
            '1966' : 4,
            '1967' : 4,
            '1968' : 4,
            '1969' : 4,
            '1970' : 4,
            '1971' : 4,
            '1972' : 4,
            '1973' : 4,
            '1974' : 4,
            '1975' : 4,
            '1976' : 4,
            '1977' : 4,
            '1978' : 4,
            '1979' : 4,
            '1980' : 4,
            '1981' : 4,
            '1982' : 4,
            '1983' : 4,
            '1984' : 4,
            '1985' : 4,
            '1986' : 4,
            '1987' : 4,
            '1988' : 4,
            '1989' : 4,
            '1990' : 4,
            '1991' : 4,
            '1992' : 4,
            '1993' : 4,
            '1994' : 4,
            '1995' : 4,
            '1996' : 4,
            '1997' : 4,
            '1998' : 4,
            '1999' : 4,
            '2000' : 4,
            '2001' : 4,
            '2002' : 4,
            '2003' : 4,
            '2004' : 4,
            '2005' : 4,
            '2006' : 4,
            '2007' : 4,
            '2008' : 4,
            '2009' : 4,
            '2010' : 4,
            '2011' : 4,
            '2012' : 4,
            '2013' : 4,
            '2014' : 4,
            '2015' : 4,
            '2016' : 4,
            '2017' : 4,
        }
    let fivePercent = {
        '1914' : 5,
        '1915' : 5,
        '1916' : 5,
        '1917' : 5,
        '1918' : 5,
        '1919' : 5,
        '1920' : 5,
        '1921' : 5,
        '1922' : 5,
        '1923' : 5,
        '1924' : 5,
        '1925' : 5,
        '1926' : 5,
        '1927' : 5,
        '1928' : 5,
        '1929' : 5,
        '1930' : 5,
        '1931' : 5,
        '1932' : 5,
        '1933' : 5,
        '1934' : 5,
        '1935' : 5,
        '1936' : 5,
        '1937' : 5,
        '1938' : 5,
        '1939' : 5,
        '1940' : 5,
        '1941' : 5,
        '1942' : 5,
        '1943' : 5,
        '1944' : 5,
        '1945' : 5,
        '1946' : 5,
        '1947' : 5,
        '1948' : 5,
        '1949' : 5,
        '1950' : 5,
        '1951' : 5,
        '1952' : 5,
        '1953' : 5,
        '1954' : 5,
        '1955' : 5,
        '1956' : 5,
        '1957' : 5,
        '1958' : 5,
        '1959' : 5,
        '1960' : 5,
        '1961' : 5,
        '1962' : 5,
        '1963' : 5,
        '1964' : 5,
        '1965' : 5,
        '1966' : 5,
        '1967' : 5,
        '1968' : 5,
        '1969' : 5,
        '1970' : 5,
        '1971' : 5,
        '1972' : 5,
        '1973' : 5,
        '1974' : 5,
        '1975' : 5,
        '1976' : 5,
        '1977' : 5,
        '1978' : 5,
        '1979' : 5,
        '1980' : 5,
        '1981' : 5,
        '1982' : 5,
        '1983' : 5,
        '1984' : 5,
        '1985' : 5,
        '1986' : 5,
        '1987' : 5,
        '1988' : 5,
        '1989' : 5,
        '1990' : 5,
        '1991' : 5,
        '1992' : 5,
        '1993' : 5,
        '1994' : 5,
        '1995' : 5,
        '1996' : 5,
        '1997' : 5,
        '1998' : 5,
        '1999' : 5,
        '2000' : 5,
        '2001' : 5,
        '2002' : 5,
        '2003' : 5,
        '2004' : 5,
        '2005' : 5,
        '2006' : 5,
        '2007' : 5,
        '2008' : 5,
        '2009' : 5,
        '2010' : 5,
        '2011' : 5,
        '2012' : 5,
        '2013' : 5,
        '2014' : 5,
        '2015' : 5,
        '2016' : 5,
        '2017' : 5,
    }
    let sixPercent = {
        '1914' : 6,
        '1915' : 6,
        '1916' : 6,
        '1917' : 6,
        '1918' : 6,
        '1919' : 6,
        '1920' : 6,
        '1921' : 6,
        '1922' : 6,
        '1923' : 6,
        '1924' : 6,
        '1925' : 6,
        '1926' : 6,
        '1927' : 6,
        '1928' : 6,
        '1929' : 6,
        '1930' : 6,
        '1931' : 6,
        '1932' : 6,
        '1933' : 6,
        '1934' : 6,
        '1935' : 6,
        '1936' : 6,
        '1937' : 6,
        '1938' : 6,
        '1939' : 6,
        '1940' : 6,
        '1941' : 6,
        '1942' : 6,
        '1943' : 6,
        '1944' : 6,
        '1945' : 6,
        '1946' : 6,
        '1947' : 6,
        '1948' : 6,
        '1949' : 6,
        '1950' : 6,
        '1951' : 6,
        '1952' : 6,
        '1953' : 6,
        '1954' : 6,
        '1955' : 6,
        '1956' : 6,
        '1957' : 6,
        '1958' : 6,
        '1959' : 6,
        '1960' : 6,
        '1961' : 6,
        '1962' : 6,
        '1963' : 6,
        '1964' : 6,
        '1965' : 6,
        '1966' : 6,
        '1967' : 6,
        '1968' : 6,
        '1969' : 6,
        '1970' : 6,
        '1971' : 6,
        '1972' : 6,
        '1973' : 6,
        '1974' : 6,
        '1975' : 6,
        '1976' : 6,
        '1977' : 6,
        '1978' : 6,
        '1979' : 6,
        '1980' : 6,
        '1981' : 6,
        '1982' : 6,
        '1983' : 6,
        '1984' : 6,
        '1985' : 6,
        '1986' : 6,
        '1987' : 6,
        '1988' : 6,
        '1989' : 6,
        '1990' : 6,
        '1991' : 6,
        '1992' : 6,
        '1993' : 6,
        '1994' : 6,
        '1995' : 6,
        '1996' : 6,
        '1997' : 6,
        '1998' : 6,
        '1999' : 6,
        '2000' : 6,
        '2001' : 6,
        '2002' : 6,
        '2003' : 6,
        '2004' : 6,
        '2005' : 6,
        '2006' : 6,
        '2007' : 6,
        '2008' : 6,
        '2009' : 6,
        '2010' : 6,
        '2011' : 6,
        '2012' : 6,
        '2013' : 6,
        '2014' : 6,
        '2015' : 6,
        '2016' : 6,
        '2017' : 6,
    }
    let sevenPercent = {
        '1914' : 7,
        '1915' : 7,
        '1916' : 7,
        '1917' : 7,
        '1918' : 7,
        '1919' : 7,
        '1920' : 7,
        '1921' : 7,
        '1922' : 7,
        '1923' : 7,
        '1924' : 7,
        '1925' : 7,
        '1926' : 7,
        '1927' : 7,
        '1928' : 7,
        '1929' : 7,
        '1930' : 7,
        '1931' : 7,
        '1932' : 7,
        '1933' : 7,
        '1934' : 7,
        '1935' : 7,
        '1936' : 7,
        '1937' : 7,
        '1938' : 7,
        '1939' : 7,
        '1940' : 7,
        '1941' : 7,
        '1942' : 7,
        '1943' : 7,
        '1944' : 7,
        '1945' : 7,
        '1946' : 7,
        '1947' : 7,
        '1948' : 7,
        '1949' : 7,
        '1950' : 7,
        '1951' : 7,
        '1952' : 7,
        '1953' : 7,
        '1954' : 7,
        '1955' : 7,
        '1956' : 7,
        '1957' : 7,
        '1958' : 7,
        '1959' : 7,
        '1960' : 7,
        '1961' : 7,
        '1962' : 7,
        '1963' : 7,
        '1964' : 7,
        '1965' : 7,
        '1966' : 7,
        '1967' : 7,
        '1968' : 7,
        '1969' : 7,
        '1970' : 7,
        '1971' : 7,
        '1972' : 7,
        '1973' : 7,
        '1974' : 7,
        '1975' : 7,
        '1976' : 7,
        '1977' : 7,
        '1978' : 7,
        '1979' : 7,
        '1980' : 7,
        '1981' : 7,
        '1982' : 7,
        '1983' : 7,
        '1984' : 7,
        '1985' : 7,
        '1986' : 7,
        '1987' : 7,
        '1988' : 7,
        '1989' : 7,
        '1990' : 7,
        '1991' : 7,
        '1992' : 7,
        '1993' : 7,
        '1994' : 7,
        '1995' : 7,
        '1996' : 7,
        '1997' : 7,
        '1998' : 7,
        '1999' : 7,
        '2000' : 7,
        '2001' : 7,
        '2002' : 7,
        '2003' : 7,
        '2004' : 7,
        '2005' : 7,
        '2006' : 7,
        '2007' : 7,
        '2008' : 7,
        '2009' : 7,
        '2010' : 7,
        '2011' : 7,
        '2012' : 7,
        '2013' : 7,
        '2014' : 7,
        '2015' : 7,
        '2016' : 7,
        '2017' : 7,
    }
    let eightPercent = {
        '1914' : 8,
        '1915' : 8,
        '1916' : 8,
        '1917' : 8,
        '1918' : 8,
        '1919' : 8,
        '1920' : 8,
        '1921' : 8,
        '1922' : 8,
        '1923' : 8,
        '1924' : 8,
        '1925' : 8,
        '1926' : 8,
        '1927' : 8,
        '1928' : 8,
        '1929' : 8,
        '1930' : 8,
        '1931' : 8,
        '1932' : 8,
        '1933' : 8,
        '1934' : 8,
        '1935' : 8,
        '1936' : 8,
        '1937' : 8,
        '1938' : 8,
        '1939' : 8,
        '1940' : 8,
        '1941' : 8,
        '1942' : 8,
        '1943' : 8,
        '1944' : 8,
        '1945' : 8,
        '1946' : 8,
        '1947' : 8,
        '1948' : 8,
        '1949' : 8,
        '1950' : 8,
        '1951' : 8,
        '1952' : 8,
        '1953' : 8,
        '1954' : 8,
        '1955' : 8,
        '1956' : 8,
        '1957' : 8,
        '1958' : 8,
        '1959' : 8,
        '1960' : 8,
        '1961' : 8,
        '1962' : 8,
        '1963' : 8,
        '1964' : 8,
        '1965' : 8,
        '1966' : 8,
        '1967' : 8,
        '1968' : 8,
        '1969' : 8,
        '1970' : 8,
        '1971' : 8,
        '1972' : 8,
        '1973' : 8,
        '1974' : 8,
        '1975' : 8,
        '1976' : 8,
        '1977' : 8,
        '1978' : 8,
        '1979' : 8,
        '1980' : 8,
        '1981' : 8,
        '1982' : 8,
        '1983' : 8,
        '1984' : 8,
        '1985' : 8,
        '1986' : 8,
        '1987' : 8,
        '1988' : 8,
        '1989' : 8,
        '1990' : 8,
        '1991' : 8,
        '1992' : 8,
        '1993' : 8,
        '1994' : 8,
        '1995' : 8,
        '1996' : 8,
        '1997' : 8,
        '1998' : 8,
        '1999' : 8,
        '2000' : 8,
        '2001' : 8,
        '2002' : 8,
        '2003' : 8,
        '2004' : 8,
        '2005' : 8,
        '2006' : 8,
        '2007' : 8,
        '2008' : 8,
        '2009' : 8,
        '2010' : 8,
        '2011' : 8,
        '2012' : 8,
        '2013' : 8,
        '2014' : 8,
        '2015' : 8,
        '2016' : 8,
        '2017' : 8,
    }
    let ninePercent = {
        '1914' : 9,
        '1915' : 9,
        '1916' : 9,
        '1917' : 9,
        '1918' : 9,
        '1919' : 9,
        '1920' : 9,
        '1921' : 9,
        '1922' : 9,
        '1923' : 9,
        '1924' : 9,
        '1925' : 9,
        '1926' : 9,
        '1927' : 9,
        '1928' : 9,
        '1929' : 9,
        '1930' : 9,
        '1931' : 9,
        '1932' : 9,
        '1933' : 9,
        '1934' : 9,
        '1935' : 9,
        '1936' : 9,
        '1937' : 9,
        '1938' : 9,
        '1939' : 9,
        '1940' : 9,
        '1941' : 9,
        '1942' : 9,
        '1943' : 9,
        '1944' : 9,
        '1945' : 9,
        '1946' : 9,
        '1947' : 9,
        '1948' : 9,
        '1949' : 9,
        '1950' : 9,
        '1951' : 9,
        '1952' : 9,
        '1953' : 9,
        '1954' : 9,
        '1955' : 9,
        '1956' : 9,
        '1957' : 9,
        '1958' : 9,
        '1959' : 9,
        '1960' : 9,
        '1961' : 9,
        '1962' : 9,
        '1963' : 9,
        '1964' : 9,
        '1965' : 9,
        '1966' : 9,
        '1967' : 9,
        '1968' : 9,
        '1969' : 9,
        '1970' : 9,
        '1971' : 9,
        '1972' : 9,
        '1973' : 9,
        '1974' : 9,
        '1975' : 9,
        '1976' : 9,
        '1977' : 9,
        '1978' : 9,
        '1979' : 9,
        '1980' : 9,
        '1981' : 9,
        '1982' : 9,
        '1983' : 9,
        '1984' : 9,
        '1985' : 9,
        '1986' : 9,
        '1987' : 9,
        '1988' : 9,
        '1989' : 9,
        '1990' : 9,
        '1991' : 9,
        '1992' : 9,
        '1993' : 9,
        '1994' : 9,
        '1995' : 9,
        '1996' : 9,
        '1997' : 9,
        '1998' : 9,
        '1999' : 9,
        '2000' : 9,
        '2001' : 9,
        '2002' : 9,
        '2003' : 9,
        '2004' : 9,
        '2005' : 9,
        '2006' : 9,
        '2007' : 9,
        '2008' : 9,
        '2009' : 9,
        '2010' : 9,
        '2011' : 9,
        '2012' : 9,
        '2013' : 9,
        '2014' : 9,
        '2015' : 9,
        '2016' : 9,
        '2017' : 9,
    }
    let tenPercent = {
        '1914' : 10,
        '1915' : 10,
        '1916' : 10,
        '1917' : 10,
        '1918' : 10,
        '1919' : 10,
        '1920' : 10,
        '1921' : 10,
        '1922' : 10,
        '1923' : 10,
        '1924' : 10,
        '1925' : 10,
        '1926' : 10,
        '1927' : 10,
        '1928' : 10,
        '1929' : 10,
        '1930' : 10,
        '1931' : 10,
        '1932' : 10,
        '1933' : 10,
        '1934' : 10,
        '1935' : 10,
        '1936' : 10,
        '1937' : 10,
        '1938' : 10,
        '1939' : 10,
        '1940' : 10,
        '1941' : 10,
        '1942' : 10,
        '1943' : 10,
        '1944' : 10,
        '1945' : 10,
        '1946' : 10,
        '1947' : 10,
        '1948' : 10,
        '1949' : 10,
        '1950' : 10,
        '1951' : 10,
        '1952' : 10,
        '1953' : 10,
        '1954' : 10,
        '1955' : 10,
        '1956' : 10,
        '1957' : 10,
        '1958' : 10,
        '1959' : 10,
        '1960' : 10,
        '1961' : 10,
        '1962' : 10,
        '1963' : 10,
        '1964' : 10,
        '1965' : 10,
        '1966' : 10,
        '1967' : 10,
        '1968' : 10,
        '1969' : 10,
        '1970' : 10,
        '1971' : 10,
        '1972' : 10,
        '1973' : 10,
        '1974' : 10,
        '1975' : 10,
        '1976' : 10,
        '1977' : 10,
        '1978' : 10,
        '1979' : 10,
        '1980' : 10,
        '1981' : 10,
        '1982' : 10,
        '1983' : 10,
        '1984' : 10,
        '1985' : 10,
        '1986' : 10,
        '1987' : 10,
        '1988' : 10,
        '1989' : 10,
        '1990' : 10,
        '1991' : 10,
        '1992' : 10,
        '1993' : 10,
        '1994' : 10,
        '1995' : 10,
        '1996' : 10,
        '1997' : 10,
        '1998' : 10,
        '1999' : 10,
        '2000' : 10,
        '2001' : 10,
        '2002' : 10,
        '2003' : 10,
        '2004' : 10,
        '2005' : 10,
        '2006' : 10,
        '2007' : 10,
        '2008' : 10,
        '2009' : 10,
        '2010' : 10,
        '2011' : 10,
        '2012' : 10,
        '2013' : 10,
        '2014' : 10,
        '2015' : 10,
        '2016' : 10,
        '2017' : 10,
    }
    let elevenPercent = {
        '1914' : 11,
        '1915' : 11,
        '1916' : 11,
        '1917' : 11,
        '1918' : 11,
        '1919' : 11,
        '1920' : 11,
        '1921' : 11,
        '1922' : 11,
        '1923' : 11,
        '1924' : 11,
        '1925' : 11,
        '1926' : 11,
        '1927' : 11,
        '1928' : 11,
        '1929' : 11,
        '1930' : 11,
        '1931' : 11,
        '1932' : 11,
        '1933' : 11,
        '1934' : 11,
        '1935' : 11,
        '1936' : 11,
        '1937' : 11,
        '1938' : 11,
        '1939' : 11,
        '1940' : 11,
        '1941' : 11,
        '1942' : 11,
        '1943' : 11,
        '1944' : 11,
        '1945' : 11,
        '1946' : 11,
        '1947' : 11,
        '1948' : 11,
        '1949' : 11,
        '1950' : 11,
        '1951' : 11,
        '1952' : 11,
        '1953' : 11,
        '1954' : 11,
        '1955' : 11,
        '1956' : 11,
        '1957' : 11,
        '1958' : 11,
        '1959' : 11,
        '1960' : 11,
        '1961' : 11,
        '1962' : 11,
        '1963' : 11,
        '1964' : 11,
        '1965' : 11,
        '1966' : 11,
        '1967' : 11,
        '1968' : 11,
        '1969' : 11,
        '1970' : 11,
        '1971' : 11,
        '1972' : 11,
        '1973' : 11,
        '1974' : 11,
        '1975' : 11,
        '1976' : 11,
        '1977' : 11,
        '1978' : 11,
        '1979' : 11,
        '1980' : 11,
        '1981' : 11,
        '1982' : 11,
        '1983' : 11,
        '1984' : 11,
        '1985' : 11,
        '1986' : 11,
        '1987' : 11,
        '1988' : 11,
        '1989' : 11,
        '1990' : 11,
        '1991' : 11,
        '1992' : 11,
        '1993' : 11,
        '1994' : 11,
        '1995' : 11,
        '1996' : 11,
        '1997' : 11,
        '1998' : 11,
        '1999' : 11,
        '2000' : 11,
        '2001' : 11,
        '2002' : 11,
        '2003' : 11,
        '2004' : 11,
        '2005' : 11,
        '2006' : 11,
        '2007' : 11,
        '2008' : 11,
        '2009' : 11,
        '2010' : 11,
        '2011' : 11,
        '2012' : 11,
        '2013' : 11,
        '2014' : 11,
        '2015' : 11,
        '2016' : 11,
        '2017' : 11,

    };
    let twelvePercent = {
        '1914' : 12,
        '1915' : 12,
        '1916' : 12,
        '1917' : 12,
        '1918' : 12,
        '1919' : 12,
        '1920' : 12,
        '1921' : 12,
        '1922' : 12,
        '1923' : 12,
        '1924' : 12,
        '1925' : 12,
        '1926' : 12,
        '1927' : 12,
        '1928' : 12,
        '1929' : 12,
        '1930' : 12,
        '1931' : 12,
        '1932' : 12,
        '1933' : 12,
        '1934' : 12,
        '1935' : 12,
        '1936' : 12,
        '1937' : 12,
        '1938' : 12,
        '1939' : 12,
        '1940' : 12,
        '1941' : 12,
        '1942' : 12,
        '1943' : 12,
        '1944' : 12,
        '1945' : 12,
        '1946' : 12,
        '1947' : 12,
        '1948' : 12,
        '1949' : 12,
        '1950' : 12,
        '1951' : 12,
        '1952' : 12,
        '1953' : 12,
        '1954' : 12,
        '1955' : 12,
        '1956' : 12,
        '1957' : 12,
        '1958' : 12,
        '1959' : 12,
        '1960' : 12,
        '1961' : 12,
        '1962' : 12,
        '1963' : 12,
        '1964' : 12,
        '1965' : 12,
        '1966' : 12,
        '1967' : 12,
        '1968' : 12,
        '1969' : 12,
        '1970' : 12,
        '1971' : 12,
        '1972' : 12,
        '1973' : 12,
        '1974' : 12,
        '1975' : 12,
        '1976' : 12,
        '1977' : 12,
        '1978' : 12,
        '1979' : 12,
        '1980' : 12,
        '1981' : 12,
        '1982' : 12,
        '1983' : 12,
        '1984' : 12,
        '1985' : 12,
        '1986' : 12,
        '1987' : 12,
        '1988' : 12,
        '1989' : 12,
        '1990' : 12,
        '1991' : 12,
        '1992' : 12,
        '1993' : 12,
        '1994' : 12,
        '1995' : 12,
        '1996' : 12,
        '1997' : 12,
        '1998' : 12,
        '1999' : 12,
        '2000' : 12,
        '2001' : 12,
        '2002' : 12,
        '2003' : 12,
        '2004' : 12,
        '2005' : 12,
        '2006' : 12,
        '2007' : 12,
        '2008' : 12,
        '2009' : 12,
        '2010' : 12,
        '2011' : 12,
        '2012' : 12,
        '2013' : 12,
        '2014' : 12,
        '2015' : 12,
        '2016' : 12,
        '2017' : 12,

    };
	let seventeenPercent = {
		'1914' : 17,
        '1915' : 17,
        '1916' : 17,
        '1917' : 17,
        '1918' : 17,
        '1919' : 17,
        '1920' : 17,
        '1921' : 17,
        '1922' : 17,
        '1923' : 17,
        '1924' : 17,
        '1925' : 17,
        '1926' : 17,
        '1927' : 17,
        '1928' : 17,
        '1929' : 17,
        '1930' : 17,
        '1931' : 17,
        '1932' : 17,
        '1933' : 17,
        '1934' : 17,
        '1935' : 17,
        '1936' : 17,
        '1937' : 17,
        '1938' : 17,
        '1939' : 17,
        '1940' : 17,
        '1941' : 17,
        '1942' : 17,
        '1943' : 17,
        '1944' : 17,
        '1945' : 17,
        '1946' : 17,
        '1947' : 17,
        '1948' : 17,
        '1949' : 17,
        '1950' : 17,
        '1951' : 17,
        '1952' : 17,
        '1953' : 17,
        '1954' : 17,
        '1955' : 17,
        '1956' : 17,
        '1957' : 17,
        '1958' : 17,
        '1959' : 17,
        '1960' : 17,
        '1961' : 17,
        '1962' : 17,
        '1963' : 17,
        '1964' : 17,
        '1965' : 17,
        '1966' : 17,
        '1967' : 17,
        '1968' : 17,
        '1969' : 17,
        '1970' : 17,
        '1971' : 17,
        '1972' : 17,
        '1973' : 17,
        '1974' : 17,
        '1975' : 17,
        '1976' : 17,
        '1977' : 17,
        '1978' : 17,
        '1979' : 17,
        '1980' : 17,
        '1981' : 17,
        '1982' : 17,
        '1983' : 17,
        '1984' : 17,
        '1985' : 17,
        '1986' : 17,
        '1987' : 17,
        '1988' : 17,
        '1989' : 17,
        '1990' : 17,
        '1991' : 17,
        '1992' : 17,
        '1993' : 17,
        '1994' : 17,
        '1995' : 17,
        '1996' : 17,
        '1997' : 17,
        '1998' : 17,
        '1999' : 17,
        '2000' : 17,
        '2001' : 17,
        '2002' : 17,
        '2003' : 17,
        '2004' : 17,
        '2005' : 17,
        '2006' : 17,
        '2007' : 17,
        '2008' : 17,
        '2009' : 17,
        '2010' : 17,
        '2011' : 17,
        '2012' : 17,
        '2013' : 17,
        '2014' : 17,
        '2015' : 17,
        '2016' : 17,
        '2017' : 17,

	}

    let markets = {
        'sAndP' : sAndP,
        '4%' : fourPercent,
        '5%' : fivePercent,
        '6%' : sixPercent,
        '7%' : sevenPercent,
        '8%' : eightPercent,
        '9%' : ninePercent,
        '10%' : tenPercent,
        '11%' : elevenPercent,
        '12%' : twelvePercent,
		'17%' : seventeenPercent,
		'seventeenPercent' : seventeenPercent,
        'tenYearBond' : tenYearBond

    }

    let inflation = {
        1914 : 1,
        1915 : 1.98,
        1916 : 12.62,
        1917 : 18.1,
        1918 : 20.44,
        1919 : 14.54,
        1920 : 2.65,
        1921 : -10.82,
        1922 : -2.31,
        1923 : 2.37,
        1924 : 0,
        1925 : 3.47,
        1926 : -1.12,
        1927 : -2.26,
        1928 : -1.16,
        1929 : 0.58,
        1930 : -6.4,
        1931 : -9.32,
        1932 : -10.27,
        1933 : 0.76,
        1934 : 1.52,
        1935 : 2.99,
        1936 : 1.45,
        1937 : 2.86,
        1938 : -2.78,
        1939 : 0,
        1940 : 0.71,
        1941 : 9.93,
        1942 : 9.03,
        1943 : 2.96,
        1944 : 2.3,
        1945 : 2.25,
        1946 : 18.13,
        1947 : 8.84,
        1948 : 2.99,
        1949 : -2.07,
        1950 : 5.93,
        1951 : 6,
        1952 : 0.75,
        1953 : 0.75,
        1954 : -0.74,
        1955 : 0.37,
        1956 : 2.99,
        1957 : 2.9,
        1958 : 1.76,
        1959 : 1.73,
        1960 : 1.36,
        1961 : 0.67,
        1962 : 1.33,
        1963 : 1.64,
        1964 : 0.97,
        1965 : 1.92,
        1966 : 3.46,
        1967 : 3.04,
        1968 : 4.72,
        1969 : 6.2,
        1970 : 5.57,
        1971 : 3.27,
        1972 : 3.41,
        1973 : 8.71,
        1974 : 12.34,
        1975 : 6.94,
        1976 : 4.86,
        1977 : 6.7,
        1978 : 9.02,
        1979 : 13.29,
        1980 : 12.52,
        1981 : 8.92,
        1982 : 3.83,
        1983 : 3.79,
        1984 : 3.95,
        1985 : 3.8,
        1986 : 1.1,
        1987 : 4.43,
        1988 : 4.42,
        1989 : 4.65,
        1990 : 6.11,
        1991 : 3.06,
        1992 : 2.9,
        1993 : 2.75,
        1994 : 2.67,
        1995 : 2.54,
        1996 : 3.32,
        1997 : 1.7,
        1998 : 1.61,
        1999 : 2.68,
        2000 : 3.39,
        2001 : 1.55,
        2002 : 2.38,
        2003 : 1.88,
        2004 : 3.26,
        2005 : 3.42,
        2006 : 2.54,
        2007 : 4.06,
        2008 : 0.1,
        2009 : 2.71,
        2010 : 1.53,
        2011 : 2.97,
        2012 : 1.73,
        2013 : 1.48,
        2014 : 0.77,
        2015 : 0.72,
        2016 : 2.07,
        2017 : 2.11
    }
    let valueOfDollar = {
        '1914' : 1.00,
        '1915' : 1.01,
        '1916' : 1.03,
        '1917' : 1.16,
        '1918' : 1.37,
        '1919' : 1.65,
        '1920' : 1.89,
        '1921' : 1.94,
        '1922' : 1.73,
        '1923' : 1.69,
        '1924' : 1.73,
        '1925' : 1.73,
        '1926' : 1.79,
        '1927' : 1.77,
        '1928' : 1.73,
        '1929' : 1.71,
        '1930' : 1.72,
        '1931' : 1.61,
        '1932' : 1.46,
        '1933' : 1.31,
        '1934' : 1.32,
        '1935' : 1.34,
        '1936' : 1.38,
        '1937' : 1.40,
        '1938' : 1.44,
        '1939' : 1.40,
        '1940' : 1.40,
        '1941' : 1.41,
        '1942' : 1.55,
        '1943' : 1.69,
        '1944' : 1.74,
        '1945' : 1.78,
        '1946' : 1.82,
        '1947' : 2.15,
        '1948' : 2.34,
        '1949' : 2.41,
        '1950' : 2.36,
        '1951' : 2.50,
        '1952' : 2.65,
        '1953' : 2.67,
        '1954' : 2.69,
        '1955' : 2.67,
        '1956' : 2.68,
        '1957' : 2.76,
        '1958' : 2.84,
        '1959' : 2.89,
        '1960' : 2.94,
        '1961' : 2.98,
        '1962' : 3.00,
        '1963' : 3.04,
        '1964' : 3.09,
        '1965' : 3.12,
        '1966' : 3.18,
        '1967' : 3.29,
        '1968' : 3.39,
        '1969' : 3.55,
        '1970' : 3.77,
        '1971' : 3.98,
        '1972' : 4.11,
        '1973' : 4.25,
        '1974' : 4.62,
        '1975' : 5.19,
        '1976' : 5.55,
        '1977' : 5.82,
        '1978' : 6.21,
        '1979' : 6.77,
        '1980' : 7.67,
        '1981' : 8.63,
        '1982' : 9.40,
        '1983' : 9.76,
        '1984' : 10.13,
        '1985' : 10.53,
        '1986' : 10.93,
        '1987' : 11.05,
        '1988' : 11.54,
        '1989' : 12.05,
        '1990' : 12.61,
        '1991' : 13.38,
        '1992' : 13.79,
        '1993' : 14.19,
        '1994' : 14.58,
        '1995' : 14.97,
        '1996' : 15.35,
        '1997' : 15.86,
        '1998' : 16.13,
        '1999' : 16.39,
        '2000' : 16.83,
        '2001' : 17.40,
        '2002' : 17.67,
        '2003' : 18.09,
        '2004' : 18.43,
        '2005' : 19.03,
        '2006' : 19.68,
        '2007' : 20.18,
        '2008' : 21.00,
        '2009' : 21.02,
        '2010' : 21.59,
        '2011' : 21.92,
        '2012' : 22.57,
        '2013' : 22.96,
        '2014' : 23.30,
        '2015' : 23.48,
        '2016' : 23.65,
        '2017' : 24.14,
        '2018' : 24.65
    }


/*---------------------------USEFUL FUNCTIONS--------------------------------*/

    function numberToDollarString(num) {
        return '$' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    function adjustForInflation(curAmnt, curYr, compYr) {
        let value = curAmnt;
        let yr = curYr;
        if (curYr < compYr) {
            for (yr; yr < compYr; yr++) {
                value = value * (1 + (inflation[yr] / 100));
            }
        }
        else {
            for (yr; yr > compYr; yr--) {
                value = value / (1 + (inflation[yr] / 100));
            }
        }
        return value;
    }

    // function adjustForInflation(curAmnt, curYr, compYr) {
    //     return curAmnt * (valueOfDollar[compYr] / valueOfDollar[curYr]);
    // }
    /* function findInflation(strOfYr, endOfYr) {
        return ((endOfYr - strOfYr) / strOfYr) * 100;
    }*/
    /* function fillInInflation() {
        let y = 1926;
        for (y; y <= 2017; y++) {
            inflation[y] = Math.round(findInflation(valueOfDollar[y], valueOfDollar[y + 1]) * 100) / 100;
        }
        console.log(inflation);
    } */



















































/*---------------------------Stuff relevant to other pages (future)----------*/


    function finalAccountBalance(initInv, yrlyWithdrwl, strtYr, numYrs) {
        let yr = strtYr;
        let balances = [];
        let balance = initInv;
        for (yr; yr < (strtYr + numYrs); yr++) {
            let percentChange = sAndP[yr];
            balance = (balance - yrlyWithdrwl) * (100 + percentChange) / 100;
            balances.push(balance);
        }
        return balance;
    }
    function minAccountBalance(initInv, yrlyWithdrwl, strtYr, numYrs) {
        let yr = strtYr;
        let balances = [];
        let balance = initInv;
        for (yr; yr < (strtYr + numYrs); yr++) {
            let percentChange = sAndP[yr];
            balance = (balance - yrlyWithdrwl) * (100 + percentChange) / 100;
            balances.push(balance);
        }
        balances.sort(function(a,b) {
            return a - b;
        })
        return balances[0];
    }



/* NO ANNUAL CONTRIBUTIONS */

    // Determine best and worst case scenerios letting money sit for X years
    function worstAndBestCases(investment, numYears) {
        function checkWorst(cur, yr) {
            if (cur < wrst) {
                wrst = cur;
                wrstYr = yr;
            }
        }
        function checkBest(cur, yr) {
            if (cur > bst) {
                bst = cur;
                bstYr = yr;
            }
        }
        let cases, i, bst, wrst, bstYr, wrstYr;
        i = 1950;
        cases = [];
        bst = -Infinity;
        wrst = Infinity;
        bstYr = 1850;
        wrstYr = 1850;
        for (i; i <= (2017 - numYears); i++) {
            let finalBalance = historicalSummary(i, (i + numYears - 1), investment);
            cases.push(finalBalance);
            checkWorst(finalBalance, i);
            checkBest(finalBalance, i);
        }
        let sortedCases = cases.sort(function(a,b) {
            return a - b;
        });
        let numCases = cases.length;
        let avgCase = cases.reduce(function(a, b) {
            return a + b;
        });
        avgCase = Math.floor(avgCase / cases.length);
        function median() {
            if (numCases % 2 === 0) {
                return cases[numCases / 2];
            }
            else {
                return cases[(numCases - 1) / 2];
            }
        }
        let medCase = median();
        // console.log(cases);
        function CAGR(start, final, years) {
            return Math.round((((Math.pow((final / start), (1/years))) - 1) * 10000), 2) / 100;
        }
        let wrstCAGR = CAGR(investment, wrst, numYears);
        let bstCAGR = CAGR(investment, bst, numYears);
        let avgCAGR = CAGR(investment, avgCase, numYears);
        let medCAGR = CAGR(investment, medCase, numYears);

        console.log(`Since 1950, the worst return on a ${investment}$ investment over ${numYears} years would have resulted in ${wrst}$ (${wrstYr}). This equates to an annualized Return of %${wrstCAGR}.
The best return would have resulted in ${bst}$ (${bstYr}), which equates to an annualized return of %${bstCAGR}.
The average return was ${avgCase} for an average annualized return of %${avgCAGR}.
The median return was ${medCase} for a median annualized return of %${medCAGR}.
*None of these values account for inflation`);
    }
    // Determine the amount of $ at endYear if initialBalance was deposited and left untouched at startYear. Also tells the low and high points through time period.
    function historicalSummary(startYear, endYear, initialBalance) {
        function checkMin(cur, yr) {
            if (cur < min) {
                min = cur;
                minYr = yr;
            }
        }
        function checkMax(cur, yr) {
            if (cur > max) {
                max = cur;
                maxYr = yr;
            }
        }
        let balance = initialBalance;
        let min = Infinity;
        let minYr = 1850;
        let max = -Infinity;
        let maxYr = 1850;
        let year = Number(startYear);
        for (year; year <= endYear; year++) {
            let thisChange = sAndP[String(year)];
            balance = Math.floor(balance * ((100 + thisChange) / 100));
            checkMin(balance, year);
            checkMax(balance, year);
            // console.log(balance);
        }
        year--;
        // console.log(`If you had invested ${initialBalance}$ in ${startYear} it would be worth ${balance} by the end of ${year}. The lowest value would have been ${min} at the end of ${minYr}. The highest value would have been ${max} at the end of ${maxYr}`)
        return balance;
    }

// });
    // worstAndBestCases(10000, 20);
    // historicalSummary(1955, 1965, 10000);
