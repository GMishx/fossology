/* **************************************************************
 Copyright (C) 2018 Siemens AG

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ************************************************************** */

#include <agent.h>
#include <emailformatter.h>

/**
 * @brief Format rows as HTML
 *
 * @param rows   rows of type column
 * @return rows in HTML table format
 */
const gchar* email_format_html(GArray* rows)
{
  guint i;
  GString* ret = g_string_new("");
  g_string_append(ret, "<tr><th>ID</th><th>Agent</th><th>Status</th></tr>\n");
  for (i = 0; i < rows->len; i++)
  {
    column* data = g_array_index(rows, column*, i);
    g_string_append_printf(ret, "<tr><td>%d</td><td>%s</td>", data->id,
                           data->agent->str);
    if (data->status == TRUE)
    {
      g_string_append(ret, "<td class='success'>COMPLETED</td>");
    }
    else
    {
      g_string_append(ret, "<td class='fail'>FAILED</td>");
    }
    g_string_append(ret, "</tr>\n");
    free(data->agent);
  }

  return ret->str;
}

/**
 * @brief Format rows as plain text
 *
 * @param rows   rows of type column
 * @return rows in plain text format
 */
const gchar* email_format_text(GArray* rows)
{
  guint i;
  GString* ret = g_string_new("");
  g_string_append(ret, "Agents run:\n");
  for (i = 0; i < rows->len; i++)
  {
    column* data = g_array_index(rows, column*, i);
    g_string_append_printf(ret, "%d => %s => ", data->id, data->agent->str);
    if (data->status == TRUE)
    {
      g_string_append(ret, "COMPLETED\n");
    }
    else
    {
      g_string_append(ret, "FAILED\n");
    }
    free(data->agent);
  }
  return ret->str;
}

