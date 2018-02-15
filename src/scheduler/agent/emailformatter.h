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

#ifndef EMAILFORMATTER_H_INCLUDE
#define EMAILFORMATTERH_INCLUDE

#include <agent.h>

typedef struct
{
  guint id;
  GString* agent;
  gboolean status;
} column;

/* Format rows as HTML */
const gchar* email_format_html(GArray* rows);
/* Format rows as plain text */
const gchar* email_format_text(GArray* rows);

#endif /* EMAILFORMATTERH_INCLUDE */
