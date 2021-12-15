export const randomColor = (index) => {
  const colors = [
    "#ffda79",
    "#2A5784",
    "#43719F",
    "#5B8DB8",
    "#7AAAD0",
    "#9BC7E4",
    "#BADDF1",
    "#E1575A",
    "#EE7423",
    "#F59D3D",
    "#FFC686",
    "#9D7760",
    "#F1CF63",
    "#7C4D79",
    "#9B6A97",
    "#BE89AC",
    "#D5A5C4",
    "#EFC9E6",
    "#BBB1AC",
    "#24693D",
    "#398949",
    "#61AA57",
    "#7DC470",
    "#B4E0A7",
    "#40407a",
    "#706fd3",
    "#34ace0",
    "#33d9b2",
    "#2c2c54",
    "#474787",
    "#227093",
    "#218c74",
    "#ff5252",
    "#ff793f",
    "#ffb142",
    "#b33939",
    "#cd6133",
    "#84817a",
    "#cc8e35",
    "#ccae62",
  ]

  if (colors[index]) {
    return colors[index]
  }
  return `#${Math.floor(Math.random() * 16777215).toString(16)}`
}

export const parseDataToGroups = (list, key = "label") => {
  return list.reduce((prev, current) => {
    ;(prev[current[key]] = prev[current[key]] || []).push(current)
    return prev
  }, {})
}
