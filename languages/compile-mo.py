#!/usr/bin/env python3
"""Compile a gettext .po file to .mo with no dependencies (no msgfmt needed).

Usage: python3 languages/compile-mo.py languages/ar.po   -> writes languages/ar.mo
Poedit / Loco Translate also write the .mo themselves; this is for editing the .po by hand.
"""
import ast
import struct
import sys


def parse_po(path):
    entries, cur, field = [], {}, None

    def flush():
        if 'msgid' in cur:
            entries.append(dict(cur))
        cur.clear()

    for raw in open(path, encoding='utf-8'):
        line = raw.strip()
        if not line:
            flush()
            field = None
            continue
        if line.startswith('#,'):
            cur['flags'] = line
            continue
        if line.startswith('#'):
            continue
        if line.startswith('"'):
            cur[field] += ast.literal_eval(line)
            continue
        key, _, value = line.partition(' ')
        if key == 'msgid' and 'msgid' in cur:
            flush()
        field = key
        cur[field] = ast.literal_eval(value)
    flush()
    return entries


def compile_mo(entries):
    messages = {}
    for e in entries:
        if 'fuzzy' in e.get('flags', ''):
            continue
        key = e['msgid']
        if 'msgctxt' in e:
            key = e['msgctxt'] + '\x04' + key
        if 'msgid_plural' in e:
            key += '\x00' + e['msgid_plural']
            forms = [e[k] for k in sorted((k for k in e if k.startswith('msgstr[')), key=lambda k: int(k[7:-1]))]
            if not any(forms):
                continue
            value = '\x00'.join(forms)
        else:
            value = e.get('msgstr', '')
            if not value and key:
                continue
        messages[key] = value

    keys = sorted(messages)
    ids = b''.join(k.encode('utf-8') + b'\x00' for k in keys)
    strs = b''.join(messages[k].encode('utf-8') + b'\x00' for k in keys)
    n = len(keys)
    key_start = 7 * 4 + 16 * n
    value_start = key_start + len(ids)
    koffsets, voffsets, ko, vo = [], [], 0, 0
    for k in keys:
        kb, vb = k.encode('utf-8'), messages[k].encode('utf-8')
        koffsets += [len(kb), key_start + ko]
        voffsets += [len(vb), value_start + vo]
        ko += len(kb) + 1
        vo += len(vb) + 1
    header = struct.pack('Iiiiiii', 0x950412de, 0, n, 7 * 4, 7 * 4 + 8 * n, 0, 0)
    return header + struct.pack('%di' % (2 * n), *koffsets) + struct.pack('%di' % (2 * n), *voffsets) + ids + strs


if __name__ == '__main__':
    src = sys.argv[1]
    out = src[:-3] + '.mo'
    data = compile_mo(parse_po(src))
    open(out, 'wb').write(data)
    print('wrote', out)
